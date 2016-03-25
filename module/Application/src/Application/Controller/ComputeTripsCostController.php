<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerNotificationsService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ComputeTripsCostController extends AbstractActionController
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
     * @var InvoicesService
     */
    private $invoicesService;

    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param TripPaymentsService $tripPaymentsService
     * @param InvoicesService $invoicesService
     * @param EntityManager $entityManager
     * @param Logger $logger
     */
    public function __construct(
        TripsService $tripsService,
        TripCostService $tripCostService,
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService,
        CustomersService $customerService,
        Logger $logger
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->customerService = $customerService;
        $this->logger = $logger;
    }

    public function computeTripsCostAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripsToBeProcessed = $this->tripsService->getTripsForCostComputation();
        $this->logger->log("Trips found: " . count($tripsToBeProcessed) . "\n");

        foreach ($tripsToBeProcessed as $trip) {
            $this->logger->log("Processing trip " . $trip->getId() . "\n");
            $this->tripCostService->computeTripCost($trip, $dryRun);
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    public function computeTripCostAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripId = $this->getRequest()->getParam('tripId');
        $trip = $this->tripsService->getTripById($tripId);
        $customer = $trip->getCustomer();

        if (!$trip->getCostComputed()) {
            $this->logger->log("Computing cost for trip " . $trip->getId() . "\n");
            $this->tripCostService->computeTripCost($trip, $dryRun);
        } else {
            $this->logger->log("Cost already computed for trip " . $trip->getId() . "\n");
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    public function invoiceTripsAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all trip_payments without invoice
        $tripPayments = $this->tripPaymentsService->getTripPaymentsNoInvoiceGrouped();
        $this->logger->log("Retrieved tripPayments\n\n");

        // generate the invoices
        $invoices = $this->invoicesService->createInvoicesForTrips($tripPayments, !$dryRun);

        $this->logger->log("Created " . count($invoices) . " invoices\n\n");
        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    public function disableLatePayersAction()
    {
        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $verbose = $request->getParam('verbose') || $request->getParam('v');

        if ($verbose) {
            $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
            $this->logger->setOutputType(Logger::TYPE_CONSOLE);
        } else {
            $this->logger->setOutputEnvironment(Logger::OUTPUT_OFF);
        }

        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all customers with expired trip payments
        $latePayers = $this->customerService->retrieveLatePayers();
        $this->logger->log('Retrieved ' . count($latePayers) . " late payers\n");

        $this->customerService->disableForLatePayment($latePayers, !$dryRun, !$dryRun);

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
