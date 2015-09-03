<?php

namespace Application\Controller;

use SharengoCore\Service\EditTripsService;

use Zend\Mvc\Controller\AbstractActionController;

class EditTripController extends AbstractActionController
{
    /**
     * @param EditTripsService
     */
    private $editTripsService;

    public function __construct(EditTripsService $editTripsService)
    {
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

        $endDate = date_create($endDateString);

        // validate date
        if (!$endDate) {
            echo "Please use a valid date format\n";
            exit;
        }

        // edit trip
        $this->editTripsService->editTrip($trip, $notPayable, $endDate);
    }
}
