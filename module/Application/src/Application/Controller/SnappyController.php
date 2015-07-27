<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use SharengoCore\Entity\Customers;

class SnappyController extends AbstractActionController
{

    private $viewRenderer;

    private $pdfService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var AuthenticationService
     */
    private $authService;

    public function __construct(
        $viewRenderer,
        $pdfService,
        $invoicesService,
        AuthenticationService $authService
    ) {
        $this->viewRenderer = $viewRenderer;
        $this->pdfService = $pdfService;
        $this->invoicesService = $invoicesService;
        $this->authService = $authService;
    }

    public function indexAction()
    {

        // get user id from AuthService
        $user = $this->authService->getIdentity();

        $invoiceId = urldecode($this->params('id'));

        $invoice = $this->invoicesService->getInvoiceById($invoiceId);

        if ($invoice != null && $user instanceof Customers && $invoice->getCustomer()->getId() == $user->getId()) {

            $this->pdfService->setOptions(array(
                'footer-right'     => '[page]', //Pag. [page]/[topage]
                'footer-left'      => 'Share`n Go s.r.l.',
                'footer-font-name' => 'Arial Sans Serif',
                'footer-font-size' => '10',
                'footer-line'      => true
            ));

            $now = new \DateTime();

            $layoutViewModel = $this->layout();
            $layoutViewModel->setTemplate('layout/pdf-layout');

            $viewModel = new ViewModel([
                'invoiceContent' => $invoice->getContent()
            ]);

            $templateVersion = $invoice->getContent()['template_version'];
            $viewModel->setTemplate('Application/Snappy/invoice-pdf-v' . $templateVersion);

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
            $this->redirect()->toRoute('login');
        }
    }
}
