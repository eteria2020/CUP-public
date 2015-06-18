<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CarsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');

        return new CarsController($carsService);
    }
}
