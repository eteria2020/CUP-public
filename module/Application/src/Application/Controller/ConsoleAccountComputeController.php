<?php

namespace Application\Controller;

use SharengoCore\Service\AccountTripsService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleAccountComputeController extends AbstractActionController
{
    /**
     * @var AccountTripsService
     */
    private $accountTripsService;

    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var TripCostService
     */
    private $tripCostService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var boolean
     */
    private $avoidPersistance;

    /**
     * @param AccountTripsService $accountTripsService
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param Logger $logger
     */
    public function __construct(
        AccountTripsService $accountTripsService,
        TripsService $tripsService,
        TripCostService $tripCostService,
        Logger $logger
    ) {
        $this->accountTripsService = $accountTripsService;
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->logger = $logger;
    }

    public function accountComputeAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $this->avoidPersistance = $request->getParam('dry-run') || $request->getParam('d');

        $this->accountTrips();
        $this->computeTripsCost();
    }

    private function accountTrips()
    {
        $this->logger->log("\nStarted accounting trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripsToBeAccounted = $this->tripsService->getTripsToBeAccounted();

        foreach ($tripsToBeAccounted as $trip) {
            $this->logger->log("Accounting trip " . $trip->getId() . "\n");
            if ($trip->isAccountable()) {
                $this->accountTripsService->accountTrip($trip, $this->avoidPersistance);
            } else {
                $this->tripsService->setTripAsNotPayable($trip);
            }
        }

        $this->logger->log("Done accounting trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
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
}
