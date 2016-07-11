<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DriversLicensePostValidationLoggerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $validationService = $serviceLocator->get('SharengoCore\Service\DriversLicenseValidationService');

        return new DriversLicensePostValidationLogger(
            $customersService,
            $validationService
        );
    }
}
