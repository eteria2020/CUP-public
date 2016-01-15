<?php

namespace Application\Controller;

use SharengoCore\Entity\TripPayments;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;

use Zend\Mvc\Controller\AbstractActionController;

class GenerateTripInvoiceController extends AbstractActionController
{
    private $tripPaymentsService;

    private $invoicesService;

    public function __construct(
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
    }

    public function generateInvoiceAction()
    {
        $tripPaymentId = $this->getRequest()->getParam('tripPaymentId');

        try {
            $tripPayment = $this->tripPaymentsService->getOneGrouped($tripPaymentId);
            $this->invoicesService->createInvoicesForTrips($tripPayment);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return "invoice created correctly\n";
    }
}
