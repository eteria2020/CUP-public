<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AddressControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $locationService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\LocationService');
        $logger = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SimpleLoggerService');

        return new AddressController(
            $entityManager,
            $tripsService,
            $locationService,
            $logger
        );
    }
}
