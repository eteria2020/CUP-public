<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RemoveGoldListTripsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $accountedTripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\AccountedTripsService');

        return new RemoveGoldListTripsController(
            $tripsService,
            $accountedTripsService
        );
    }
}
