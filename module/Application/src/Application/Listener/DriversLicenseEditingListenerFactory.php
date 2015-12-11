<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DriversLicenseEditingListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $customerDeactivator = $serviceLocator->get('SharengoCore\Service\CustomerDeactivationService');

        return new DriversLicenseEditingListener(
            $customersService,
            $customerDeactivator
        );
    }
}
