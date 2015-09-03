<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditTripControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\EditTripsService');

        return new EditTripController($tripsService);
    }
}
