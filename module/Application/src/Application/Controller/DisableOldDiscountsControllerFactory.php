<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DisableOldDiscountsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\customersService');
        $disableOldDiscountsService = $sharedLocator->get('SharengoCore\Service\OldCustomerDiscountsService');
        $logger = $sharedLocator->get('SharengoCore\Service\SimpleLoggerService');

        return new DisableOldDiscountsController(
            $customersService,
            $disableOldDiscountsService,
            $logger
        );
    }
}
