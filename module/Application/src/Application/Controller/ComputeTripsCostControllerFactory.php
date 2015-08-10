<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ComputeTripsCostControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $tripCostService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripCostService');

        return new ComputeTripsCostController($tripsService, $tripCostService);
    }
}
