<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;
use SharengoCore\Service\EditTripsService;

use Zend\Mvc\Controller\AbstractActionController;

class EditTripController extends AbstractActionController
{
    /**
     * @param TripsService
     */
    private $tripsService;

    /**
     * @param EditTripsService
     */
    private $editTripsService;

    /**
     * @param TripsService $tripsService
     * @param EditTripsService $editTripsService
     */
    public function __construct(
        TripsService $tripsService,
        EditTripsService $editTripsService
    ) {
        $this->tripsService = $tripsService;
        $this->editTripsService = $editTripsService;
    }

    public function editTripAction()
    {
        $tripId = $this->request->getParam('tripId');
        $notPayable = $this->request->getParam('notPayable');
        $endDateString = $this->request->getParam('endDate');

        $trip = $this->tripsService->getTripById($tripId);

        // validate trip
        if (!$trip) {
            echo "There is no trip with the requested id\n";
            exit;
        }

        $endDate = is_null($endDateString) ? null : date_create($endDateString);

        // validate date
        if ($endDate === false) {
            echo "Please use a valid date format\n";
            exit;
        }

        // edit trip
        $this->editTripsService->editTrip($trip, $notPayable, $endDate);
    }
}
