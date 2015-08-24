<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use SharengoCore\Service\CarsService;
use SharengoCore\Service\LocationService;
use SharengoCore\Service\SimpleLoggerService as Logger;

use Zend\Mvc\Controller\AbstractActionController;

class PolygonController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CarsService
     */
    private $carsService;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityManager $entityManager,
        CarsService $carsService,
        LocationService $locationService,
        Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->carsService = $carsService;
        $this->locationService = $locationService;
        $this->logger = $logger;
    }

    public function evaluateCarsLocationAction()
    {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $dryRun = $request->getParam('dry-run') || $request->getParam('d');
        $this->logger->log("\nStarted\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");

        // Get polygons
        //
        // Get all cars that can be evaluated
        //
        // Loop through cars
        //
        // Check if car is in any polygon
        //
        // If it is set car statuses accordingly

        if (!$dryRun) {
            $this->logger->log("EntityManager: flushing\n\n");
            $this->entityManager->flush();
        }

        $this->logger->log("Done\ntime = " . date_create()->format('Y-m-d H:i:s') . "\n\n");
    }
}
