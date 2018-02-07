<?php

namespace Application\Controller;

use SharengoCore\Entity\TripPayments;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class GenerateTripInvoiceController extends AbstractActionController
{
    private $tripPaymentsService;

    private $invoicesService;

    private $logger;

    public function __construct(
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService,
        Logger $logger
    ) {
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
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

    public function generateTripInvoicesAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        /* get all parameters */

        $time = $this->getRequest()->getParam('time');
        $avoidPersistance = $this->getRequest()->getParam('no-db') || $this->getRequest()->getParam('d');

        switch ($time){
            case "daily":
                $firstDay = null;
                $lastDay = null;
                break;
            case "monthly": //previous month
                $firstDay = new \DateTime('first day of previous month');
                $lastDay = new \DateTime('last day of previous month');
                break;
            default:
                $this->logger->log("Error: missing time parameter\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
                exit();
        }

        $this->logger->log("Started generating invoices\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all trip_payments without invoice
        $tripPayments = $this->tripPaymentsService->getTripPaymentsNoInvoiceGrouped($firstDay, $lastDay);
        $this->logger->log("Generating invoices for " . count($tripPayments) . "trips - ". date_create()->format('Y-m-d H:i:s')."\n");

        // generate the invoices
        $invoices = $this->invoicesService->createInvoicesForTrips($tripPayments, !$avoidPersistance, $lastDay);

        $this->logger->log("Done generating invoices\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
