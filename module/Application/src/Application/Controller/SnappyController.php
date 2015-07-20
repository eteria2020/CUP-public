<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SnappyController extends AbstractActionController
{

    private $viewRenderer;
    private $pdfService;

    public function __construct($viewRenderer, $pdfService)
    {
        $this->viewRenderer = $viewRenderer;
        $this->pdfService = $pdfService;
    }

    public function indexAction()
    {

        return $this->testPdf();
    }

    public function testPdf()
    {

        $now = new \DateTime();

        $layoutViewModel = $this->layout();
        $layoutViewModel->setTemplate('layout/pdf-layout');

        $viewModel = new ViewModel();

        $viewModel->setTemplate('Application/Snappy/pdf-template');

        $layoutViewModel->setVariables(array(
            'content' => $this->viewRenderer->render($viewModel),
        ));

        $htmlOutput = $this->viewRenderer->render($layoutViewModel);

        $output = $this->pdfService->getOutputFromHtml($htmlOutput);

        $response = $this->getResponse();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/pdf');
        $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"export-" . $now->format('d-m-Y H:i:s') . ".pdf\"");

        $response->setContent($output);

        return $response;

    }
}
