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
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        $delay = 100000; // half second in microseconds

        $trips = $this->tripsService->getTripsNoAddress();

        foreach ($trips as $trip) {
            $this->logger->log("Parsing trip: " . $trip->getId() . "\n");

            $addressBeginning = $this->locationService->getAddressFromCoordinates(
                $trip->getLatitudeBeginning(),
                $trip->getLongitudeBeginning()
            );
            $this->logger->log("Beginning: " . $addressBeginning . "\n");
            $trip->setAddressBeginning($addressBeginning);

            $addressEnd = $this->locationService->getAddressFromCoordinates(
                $trip->getLatitudeEnd(),
                $trip->getLongitudeEnd()
            );
            $this->logger->log("End: " . $addressEnd . "\n");
            $trip->setAddressEnd($addressEnd);

            $this->logger->log("EntityManager: persisting\n\n");
            $this->entityManager->persist($trip);

            usleep($delay);
        }

        if (!$dryRun) {
            $this->logger->log("EntityManager: flushing\n\n");
            $this->entityManager->flush();
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
