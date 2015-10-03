<?php

namespace Application\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AvailableFleetsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $authenticationService = $sharedLocator->get('zfcuser_auth_service');
        $fleetService = $sharedLocator->get('SharengoCore\Service\FleetService');
        $request = $sharedLocator->get('Request');

        return new AvailableFleets(
            $authenticationService,
            $fleetService,
            $request
        );
    }
}
