<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiscountStatusControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $discountStatusService = $sharedLocator->get('SharengoCore\Service\DiscountStatusService');

        return new DiscountStatusController(
            $customersService,
            $discountStatusService
        );
    }
}
