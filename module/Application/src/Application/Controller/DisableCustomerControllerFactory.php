<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DisableCustomerControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedServiceLocator->get('SharengoCore\Service\CustomersService');
        $customerDeactivationService = $sharedServiceLocator->get('SharengoCore\Service\CustomerDeactivationService');

        return new DisableCustomerController(
            $customersService,
            $customerDeactivationService
        );
    }
}
