<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SharengoCore\Service\EmailService;
use SharengoCore\Service\FleetService;

class PartnerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $partnerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PartnerService');
        
        return new PartnerController(
            $partnerService
        );
    }
}
