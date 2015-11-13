<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\AccountedTripsService;

use Zend\Mvc\Controller\AbstractActionController;

class RemoveGoldListTripsController extends AbstractActionController
{
    /**
     * @var TripsService
     */
    private $tripsService;

    /**
     * @var AccountedTripsService
     */
    private $accountedTripsService;

    /**
     * @param TripsService $tripsService
     * @param AccountedTripsService $accountedTripsService
     */
    public function __construct(
        TripsService $tripsService,
        AccountedTripsService $accountedTripsService
    ) {
        $this->tripsService = $tripsService;
        $this->accountedTripsService = $accountedTripsService;
    }

    public function removeGoldListTripsAction()
    {
        $dryRun = $this->getRequest()->getParam('dry-run');
        $verbose = $this->getRequest()->getParam('verbose');

        $trips = $this->tripsService->getTripsByUsersInGoldList();

        $tripIds = array_map(function ($trip) {
            return $trip->getId();
        }, $trips);

        if ($verbose) {
            fwrite(STDOUT, "I'm going to process ".count($tripIds)." trips\n");
        }

        if ($dryRun) {
            fwrite(STDOUT, "I would remove the accounting of the following trips:\n");
            fwrite(STDOUT, implode(', ', $tripIds)."\n");
        } else {
            $this->tripsService->setTripsAsNotPayable($tripIds);
            $this->accountedTripsService->removeAccountedTrips($tripIds);
        }

        fwrite(STDOUT, "Completed\n");
    }
}
