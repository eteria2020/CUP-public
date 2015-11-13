<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\AccountTripsService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleAccountComputeController extends AbstractActionController
{
    /**
     * @var CustomersService
     */
    private $customerService;

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
     * @param CustomersService $customersService
     * @param AccountTripsService $accountTripsService
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param Logger $logger
     */
    public function __construct(
        CustomersService $customerService,
        AccountTripsService $accountTripsService,
        TripsService $tripsService,
        TripCostService $tripCostService,
        Logger $logger
    ) {
        $this->customerService = $customerService;
        $this->accountTripsService = $accountTripsService;
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->logger = $logger;
    }

    public function accountComputeAction()
    {
        $this->prepareLogger();
        $this->checkDryRun();

        $this->accountTrips();
        $this->computeTripsCost();
    }

    /*
     * Account trips
     *
     * The first time this action is called on a fresh database, make sure
     * trips before 05/07 are excluded (ie payable = false).
     *
     */
    public function accountTripsAction()
    {

        $this->prepareLogger();
        $this->checkDryRun();

        $this->accountTrips();
    }

    public function accountTripAction()
    {

        $this->prepareLogger();
        $this->checkDryRun();

        $this->logger->log("\nStarted accounting trip\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $tripId = $this->getRequest()->getParam('tripId');

        $trip = $this->tripsService->getTripById($tripId);

        if ($trip->isAccountable()) {
            $this->accountTripsService->accountTrip($trip, $this->avoidPersistance);
        } else {
            $this->logger->log("Trip ".$tripId." not accountable\n");
        }

        $this->logger->log("Done accounting trip\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }

    public function accountUserTripsAction()
    {

        $this->prepareLogger();
        $this->checkDryRun();

        $this->logger->log("\nStarted accounting user trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $customerId = $this->getRequest()->getParam('customerId');

        $customer = $this->customerService->findById($customerId);

        $tripsToBeAccounted = $this->tripsService->getCustomerTripsToBeAccounted($customer);

        foreach ($tripsToBeAccounted as $trip) {
            $this->logger->log("Accounting trip " . $trip->getId() . "\n");
            $this->accountTripsService->accountTrip($trip, $this->avoidPersistance);
        }

        $this->logger->log("Done accounting user trips\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
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
                if (!$this->avoidPersistance) {
                    $this->tripsService->setTripAsNotPayable($trip);
                }
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

    private function prepareLogger()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    private function checkDryRun()
    {
        $request = $this->getRequest();
        $this->avoidPersistance = $request->getParam('dry-run') || $request->getParam('d');
    }
}
