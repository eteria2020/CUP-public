<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

use SharengoCore\Service\CarsService;

class CarsController extends AbstractActionController
{

    /**
     * @var SharengoCore\Service\CarsService
     */
    private $carsService;

    public function __construct(CarsService $carsService) {
        $this->carsService = $carsService;
    }

    // when this becomes more serious maybe it will be better move it in some better place, like an API module
    public function positionsAction()
    {
        $cars = $this->carsService->getListCars();

        /*
        $positions = array();
        
        foreach($cars as $car)
        {
            $positions[] = [$car->getLatitude(), $car->getLongitude()];
        }
        */

        $elements = array();

        foreach($cars as $car)
        {
            $elements[] = [
                'position' => [$car->getLatitude(), $car->getLongitude()],
                'plate' => $car->getPlate(),
                'intCleanliness' => $car->getIntCleanliness(),
                'extCleanliness' => $car->getExtCleanliness(),
                'location' => $car->getLocation(),
                'km' => $car->getKm()
            ];
        }

        return new JsonModel($elements);
    }
}
