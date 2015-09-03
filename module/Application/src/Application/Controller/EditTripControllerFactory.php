<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditTripControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $tripsService = $sharedLocator->get('SharengoCore\Service\TripsService');
        $editTripsService = $sharedLocator->get('SharengoCore\Service\EditTripsService');

        return new EditTripController($tripsService, $editTripsService);
    }
}
