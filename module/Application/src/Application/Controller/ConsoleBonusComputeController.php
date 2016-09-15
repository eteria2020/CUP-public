<?php

namespace Application\Controller;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\AccountTripsService;
use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;
use SharengoCore\Service\BonusService;
use SharengoCore\Service\EventsService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleBonusComputeController extends AbstractActionController
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
    //private $tripCostService;

    /**
     * @var BonusService
     */
    private $bonusService;
    
    /**
     * @var EventsService
     */
    private $eventsService;

    /**
     * @var Logger
     */
    private $logger;
    
   
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
        BonusService $bonusService,
        EventsService $eventsService,
        Logger $logger
    ) {
        $this->customerService = $customerService;
        $this->accountTripsService = $accountTripsService;
        $this->tripsService = $tripsService;
        //$this->tripCostService = $tripCostService;
        $this->bonusService = $bonusService;
        $this->eventsService = $eventsService;
        $this->logger = $logger;
    }

    public function bonusComputeAction()
    {
        
        $id = $this->params()->fromRoute('id', 0);

        $trip = $this->tripsService->getTripById($id);

        if (!$trip instanceof Trips) {
            throw new TripNotFoundException();
        }

        $events = $this->eventsService->getEventsByTrip($trip);
        
        $this->prepareLogger();
        $this->checkDryRun();

        //$this->accountTrips();
        //$this->computeTripsCost();
    }

    /*private function accountTrips()
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
    }*/    

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
