<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\TripCostService;

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

    public function __construct(
        TripsService $tripsService,
        TripCostService $tripCostService
    ) {
        $this->tripsService = $tripsService;
        $this->tripCostService = $tripCostService;
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
}
