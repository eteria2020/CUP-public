<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use Doctrine\ORM\EntityManager;
use SharengoCore\Service\LocationService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class AddressController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param EntityManager $entityManager
     * @param TripsService $tripsService
     * @param LocationService $locationService
     * @param Logger $logger
     */
    public function __construct(
        EntityManager $entityManager,
        TripsService $tripsService,
        LocationService $locationService,
        Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->tripsService = $tripsService;
        $this->locationService = $locationService;
        $this->logger = $logger;
    }

    public function generateLocationsAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $trips = $this->tripsService->getTripsNoAddress();

        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;generateLocationsAction;start;".count($trips).";".$dryRun."\n");
        foreach ($trips as $trip) {
            $this->tripsService->setAddressByGeocode($trip, $dryRun);
            $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;generateLocationsAction;locate;".$trip->getId().";".$trip->getAddressBeginning().";".$trip->getAddressEnd()."\n");
        }

        $this->logger->log(date_create()->format('y-m-d H:i:s').";INF;generateLocationsAction;end\n");
    }
}
