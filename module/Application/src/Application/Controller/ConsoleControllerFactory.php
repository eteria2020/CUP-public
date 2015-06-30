<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $profilingPlatformService =  $serviceLocator->getServiceLocator()->get('ProfilingPlatformService');
   		$config = $serviceLocator->getServiceLocator()->get('Config');
   		$battery = $config['alarm']['battery'];
   		$delay = $config['alarm']['delay'];

        return new ConsoleController($customerService, $carsService, $entityManager, $profilingPlatformService, $battery, $delay);
    }
}
