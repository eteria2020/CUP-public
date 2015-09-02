<?php

namespace Application\Controller;

use SharengoCore\Service\TripsService;

use Zend\Mvc\Controller\AbstractActionController;

class EditTripController extends AbstractActionController
{
    /**
     * @param TripsService
     */
    private $tripsService;

    public function __construct(TripsService $tripsService)
    {
        $this->tripsService = $tripsService;
    }

    public function editTripAction()
    {
        $tripId = $this->request->getParam('tripId');
        $notPayable = $this->request->getParam('notPayable');
        $endDateString = $this->request->getParam('endDate');

        $trip = $this->tripsService->getTripById($tripId);

        if (!$trip) {
            echo "There is no trip with the requested id\n";
            exit;
        }

        $endDate = date_create($endDateString);

        if (!$endDate) {
            echo "Please use a valid date format\n";
            exit;
        }

        $this->tripsService->editTrip($trip, $notPayable, $endDate);
    }
}
