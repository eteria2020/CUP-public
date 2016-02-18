<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DriversLicenseValidationListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $enqueueValidation = $serviceLocator->get('MvLabsDriversLicenseValidation\EnqueueValidation');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');

        return new DriversLicenseValidationListener(
            $enqueueValidation,
            $customersService
        );
    }
}
