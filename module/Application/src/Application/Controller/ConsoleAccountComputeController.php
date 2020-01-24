<?php

namespace Application\Controller;
use SharengoCore\Entity\CustomerDeactivation;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CustomerDeactivationService;
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
     * @var CustomerDeactivationService
     */
    private $customerDeactivationService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var boolean
     */
    private $avoidPersistance;

    /**
     * ConsoleAccountComputeController constructor.
     * @param CustomersService $customerService
     * @param AccountTripsService $accountTripsService
     * @param TripsService $tripsService
     * @param TripCostService $tripCostService
     * @param CustomerDeactivationService $customerDeactivationService
     * @param Logger $logger
     */
    public function __construct(
        CustomersService $customerService,
        AccountTripsService $accountTripsService,
        TripsService $tripsService,
        TripCostService $tripCostService,
        CustomerDeactivationService $customerDeactivationService,
        Logger $logger
    ) {
        $this->customerService = $customerService;
        $this->accountTripsService = $accountTripsService;
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
        $this->customerDeactivationService = $customerDeactivationService;
        $this->logger = $logger;
    }

    /**
     * @deprecated This action was been override from public-business-module
     * @throws \Exception
     */
    public function accountComputeAction()
    {
        $this->prepareLogger();
        $this->checkDryRun();

        $this->accountTrips();
        return;
        $tripsForCostComputation = $this->computeTripsCost();
        $this->checkCustomerBonusThreshold($tripsForCostComputation);
    }

    /**
     * Account trips
     *
     * The first time this action is called on a fresh database, make sure
     * trips before 05/07 are excluded (ie payable = false).
     *
     * @deprecated This action was been override from public-business-module
     * @throws \Exception
     */
    public function accountTripsAction()
    {

        $this->prepareLogger();
        $this->checkDryRun();

        $this->accountTrips();
    }

    /**
     * Account trip
     *
     * @deprecated This action was been override from public-business-module
     * @throws \Exception
     */
    public function accountTripAction()
    {

        $this->prepareLogger();
        $this->checkDryRun();

        $this->logger->log(sprintf("%s;INF;accountTripAction;start\n",date('ymd-His')));
        $tripId = $this->getRequest()->getParam('tripId');
        $trip = $this->tripsService->getTripById($tripId);
        $this->logger->log(sprintf("%s;INF;accountTripAction;%d;%b\n",date('ymd-His'),$tripId, $trip->isAccountable()));

        if ($trip->isAccountable()) {
            $this->accountTripsService->accountTrip($trip, $this->avoidPersistance);
        }

        $this->logger->log(sprintf("%s;INF;accountTripAction;end\n",date('ymd-His')));
    }

    /**
     * @throws \Exception
     */
    public function accountUserTripsAction()
    {

        $this->prepareLogger();
        $this->checkDryRun();

        $this->logger->log(sprintf("%s;INF;accountUserTripsAction;start\n",date('ymd-His')));
        $customerId = $this->getRequest()->getParam('customerId');
        $customer = $this->customerService->findById($customerId);

        $tripsToBeAccounted = $this->tripsService->getCustomerTripsToBeAccounted($customer);

        foreach ($tripsToBeAccounted as $trip) {
            $this->logger->log(sprintf("%s;INF;accountUserTripsAction;trip;%d\n",date('ymd-His'),$trip->getId()));
            $this->accountTripsService->accountTrip($trip, $this->avoidPersistance);
        }

        $this->logger->log(sprintf("%s;INF;accountUserTripsAction;end\n",date('ymd-His')));
    }

    /**
     *  Account the trips tha cab be accounted
     *
     * @return array
     * @throws \Exception
     */
    private function accountTrips()
    {
        $this->logger->log(sprintf("%s;INF;accountTrips;start\n",date('ymd-His')));
        $tripsToBeAccounted = $this->tripsService->getTripsToBeAccounted();

        foreach ($tripsToBeAccounted as $trip) {
            $this->logger->log(sprintf("%s;INF;accountTrips;trip;%d;%b\n",date('ymd-His'),$trip->getId(),$trip->isAccountable()));
            if ($trip->isAccountable()) {
                $this->accountTripsService->accountTrip($trip, $this->avoidPersistance);
            } else {
                if (!$this->avoidPersistance) {
                    $this->tripsService->setTripAsNotPayable($trip);
                }
            }
        }

        $this->logger->log(sprintf("%s;INF;accountTrips;end\n",date('ymd-His')));
        return $tripsToBeAccounted;
    }

    /**
     * @return \SharengoCore\Entity\Trips[]
     * @throws \Exception
     */
    private function computeTripsCost()
    {
        $this->logger->log(sprintf("%s;INF;computeTripsCost;start\n",date('ymd-His')));
        $tripsForCostComputation = $this->tripsService->getTripsForCostComputation();
        $this->logger->log(sprintf("%s;INF;computeTripsCost;trips;%d\n",date('ymd-His'), count($tripsForCostComputation)));

        foreach ($tripsForCostComputation as $trip) {
            $this->logger->log(sprintf("%s;INF;computeTripsCost;trip;%d\n",date('ymd-His'), $trip->getId()));
            $this->tripCostService->computeTripCost($trip, $this->avoidPersistance);
        }

        $this->logger->log(sprintf("%s;INF;computeTripsCost;end\n",date('ymd-His')));
        return $tripsForCostComputation;
    }

    /**
     * Check the customer bonus are bellow the threshold, then deactive the user
     * @param $tripsForCostComputation
     */
    private function checkCustomerBonusThreshold($tripsForCostComputation)
    {
        $customerBonusThreshold = 15;
        $this->logger->log(sprintf("%s;INF;tripsForCostComputation;start;%d\n",date('ymd-His'),$customerBonusThreshold));

        foreach ($tripsForCostComputation as $trip) {
            $customer = $trip->getCustomer();
            if($customerBonusThreshold > $customer->getTotalBonuses()) {
                $this->logger->log(sprintf("%s;INF;tripsForCostComputation;trip;%d;customer;%d\n",date('ymd-His'),$trip->getId(), $customer->getId()));
                $this->customerDeactivationService->deactivateForCustomerBonusThreshold($customer);
            }
        }

        $this->logger->log(sprintf("%s;INF;tripsForCostComputation;end\n",date('ymd-His')));
    }

    /**
     * Prepare the logger
     */
    private function prepareLogger()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);
    }

    /**
     * Check for dry run flag
     */
    private function checkDryRun()
    {
        $request = $this->getRequest();
        $this->avoidPersistance = $request->getParam('dry-run') || $request->getParam('d');
    }
}
