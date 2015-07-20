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
        //return $this->downloadFile();
        /*return */$this->testPdf();
    }

    private function testPdf()
    {

        $now = new \DateTime();

        $layoutViewModel = $this->layout();
        $layoutViewModel->setTemplate('layout/pdf-layout');

        $viewModel = new ViewModel();

        $viewModel->setTemplate('Application/Snappy/pdf-template');

        $layoutViewModel->setVariables([
            'content' => $this->viewRenderer->render($viewModel),
        ]);
/*
        $htmlOutput = $this->viewRenderer->render($layoutViewModel);

        $output = $this->pdfService->getOutputFromHtml($htmlOutput);

        $response = $this->getResponse();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/pdf');
        $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"export-" . $now->format('d-m-Y H:i:s') . ".pdf\"");

        $response->setStream($output);

        return $response;
*/
    }

    private function downloadFile()
    {
        /* added */
        $viewModel = new ViewModel();
        $viewModel->setTemplate('layout/pdf-layout');
        $viewModel->setVariables([
            'content' => $this->viewRenderer->render($viewModel),
        ]);
        $htmlOutput = $this->viewRenderer->render($viewModel);
        $pdfOutput = $this->pdfService->getOutputFromHtml($htmlOutput);
        /* /added */

        //$file = 'Application/Controller/SnappyController.php';
        $response = new \Zend\Http\Response\Stream();
        //$response->setStream(fopen($file, 'r'));
        $response->setStream($pdfOutput); /* added */
        $response->setStatusCode(200);
        //$response->setStreamName(basename($file));
        $response->setStreamName('test1'); /* added */
        $headers = new \Zend\Http\Headers();
        $headers->addHeaders([
            'Content-Disposition' => 'attachment; filename="export-' . $now->format('d-m-Y H:i:s') . '.pdf',
            'Content-Type' => 'application/octet-stream',
            //'Content-Length' => filesize($file),
            'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ]);
        $response->setHeaders($headers);
        return $response;
    }
}
