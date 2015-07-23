<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SnappyController extends AbstractActionController
{

    private $viewRenderer;

    private $pdfService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    public function __construct(
        $viewRenderer,
        $pdfService,
        $invoicesService
    ) {
        $this->viewRenderer = $viewRenderer;
        $this->pdfService = $pdfService;
        $this->invoicesService = $invoicesService;
    }

    public function indexAction()
    {

        $this->pdfService->setOptions(array(
            'footer-right'     => '[page]', //Pag. [page]/[topage]
            'footer-left'      => 'Share`n Go s.r.l.',
            'footer-font-name' => 'Arial Sans Serif',
            'footer-font-size' => '10',
            'footer-line'      => true
        ));

        $invoiceId = urldecode($this->params('id'));

        $invoice = $this->invoicesService->getInvoiceById($invoiceId);
        if ($invoice != null) {

            $now = new \DateTime();

            $layoutViewModel = $this->layout();
            $layoutViewModel->setTemplate('layout/pdf-layout');

            $viewModel = new ViewModel([
                'invoiceContent' => $invoice->getContent()
            ]);

            $viewModel->setTemplate('Application/Snappy/test1');

            $layoutViewModel->setVariables([
                'content' => $this->viewRenderer->render($viewModel)
            ]);

            $htmlOutput = $this->viewRenderer->render($layoutViewModel);
            //echo $htmlOutput;die;

            $output = $this->pdfService->getOutputFromHtml($htmlOutput);
            $response = $this->getResponse();
            $headers  = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'application/pdf');
            $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"export-" . $now->format('d-m-Y H:i:s') . ".pdf\"");
            $headers->addHeaderLine('Content-Length', strlen($output));

            $response->setContent($output);

            return $response;

        } else {
            //return error
        }
    }
}
