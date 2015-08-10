<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\TripPaymentsService;
use SharengoCore\Service\InvoicesService;
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
     * @var boolean defines verbosity
     */
    private $verbose;

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
     */
    public function __construct(
        TripsService $tripsService,
        TripCostService $tripCostService,
        TripPaymentsService $tripPaymentsService,
        InvoicesService $invoicesService,
        Logger $logger
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->tripPaymentsService = $tripPaymentsService;
        $this->invoicesService = $invoicesService;
        $this->logger = $logger;
    }

    public function computeTripsCostAction()
    {
        $tripsToBeProcessed = $this->tripsService->getTripsForCostComputation();

        foreach ($tripsToBeProcessed as $trip) {
            echo "processing trip ".$trip->getId()."\n";
            $this->tripCostService->computeTripCost($trip);
        }

        echo "\nDONE\n";
    }

    public function invoiceTripsAction()
    {
        $this->logger->setOutputEnviornment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');

        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // get all trip_payments without invoice
        $tripPayments = $this->tripPaymentsService->getTripPaymentsNoInvoiceGrouped();
        $this->logger->log('Retrieved ' . count($tripPayments) . " tripPayments\n");

        // generate the invoices
        $invoices = $this->invoicesService->createInvoicesForTrips($tripPayments, !$dryRun);

        $this->logger->log("Created " . count($invoices) . " invoices\n\n");
        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
