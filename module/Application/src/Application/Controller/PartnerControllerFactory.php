<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\FleetService;

class PartnerControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        
        return new PartnerController(
            $customerService
        );
    }
}
