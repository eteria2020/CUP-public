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

        return new IndexController($mobileUrl,$zoneService);
    }
}
