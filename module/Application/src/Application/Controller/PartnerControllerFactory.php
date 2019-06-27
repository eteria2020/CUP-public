<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PartnerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //$entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $loggerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SimpleLoggerService');
        $partnerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PartnerService');
        $smsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SmsService');

        return new PartnerController(
            $loggerService,
            $partnerService,
            $smsService
        );
    }
}
