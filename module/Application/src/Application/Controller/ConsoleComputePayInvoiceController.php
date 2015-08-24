<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\PaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleComputePayInvoiceController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var TripCostService
     */
    private $tripCostService;

    /**
     * @var TripPaymentsService
     */
    private $tripPaymentsService;

    /**
     * @var PaymentsService
     */
    private $paymentsService;

    /**
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var boolean
     */
    private $avoidEmails;

    /**
     * @var boolean
     */
    private $avoidCartasi;

    /**
     * @var boolean
     */
    private $avoidPersistance;

    /**
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param Logger $logger
     */
    public function __construct(
        TripsService $tripsService,
        TripCostService $tripCostService,
        TripPaymentsService $tripPaymentsService,
        PaymentsService $paymentsService,
        InvoicesService $invoicesService,
        Logger $logger
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->paymentsService = $paymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
    }

    public function computePayInvoiceAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->avoidEmails = $request->getParam('no-emails') || $request->getParam('e');
        $this->avoidCartasi = $request->getParam('no-cartasi') || $request->getParam('c');
        $this->avoidPersistance = $request->getParam('no-db') || $request->getParam('d');

        $this->computeTripsCost();
        $this->processPayments();
        $this->generateInvoices();
    }

    private function computeTripsCost()
    {
        $this->logger->log("\nStarted computing costs\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripsForCostComputation = $this->tripsService->getTripsForCostComputation();
        $this->logger->log("Computing cost for " . count($tripsForCostComputation) . " trips\n");

        foreach ($tripsForCostComputation as $trip) {
            $this->logger->log("Computing cost for trip " . $trip->getId() . "\n");
            $this->tripCostService->computeTripCost($trip, $this->avoidPersistance);
        }

        $this->logger->log("Done computing costs\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    private function processPayments()
    {
        $this->logger->log("\nStarted processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripsPayments = $this->tripPaymentsService->getTripPaymentsForPayment();
        $this->logger->log("Processing payments for " . count($tripsPayments) . "trips\n");

        foreach ($tripsPayments as $tripPayment) {
            $this->logger->log("Processing payment for trip payment " . $tripPayment->getId() . "\n");
            $this->paymentsService->tryPayment(
                $tripPayment,
                $this->avoidEmails,
                $this->avoidCartasi,
                $this->avoidPersistance
            );
        }

        $this->logger->log("Done processing payments\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    private function generateInvoices()
    {
        $this->logger->log("\nStarted generating invoices\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all trip_payments without invoice
        $tripPayments = $this->tripPaymentsService->getTripPaymentsNoInvoiceGrouped();
        $this->logger->log("Generating invoices for " . count($tripsPayments) . "trips\n");

        // generate the invoices
        $invoices = $this->invoicesService->createInvoicesForTrips($tripPayments, !$this->avoidPersistance);

        $this->logger->log("Done generating invoices\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
