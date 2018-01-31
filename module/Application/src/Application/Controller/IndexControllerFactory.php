<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $mobileUrl = $config['mobile']['url'];
        $zoneService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ZonesService');
        $carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        $fleetService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\FleetService');
        $poisService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PoisService');
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');

        return new IndexController(
            $mobileUrl,
            $zoneService,
            $carsService,
            $fleetService,
            $poisService,
            $customersService
        );
    }
}
