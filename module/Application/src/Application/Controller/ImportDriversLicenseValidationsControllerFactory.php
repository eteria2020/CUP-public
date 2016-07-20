<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImportDriversLicenseValidationsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $customersService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $validationService = $sharedServiceManager->get('SharengoCore\Service\DriversLicenseValidationService');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');
        $config = $sharedServiceManager->get('Config');
        $validationConfig = $config['drivers-license-validation'];

        return new ImportDriversLicenseValidationsController(
            $customersService,
            $validationService,
            $logger,
            $validationConfig
        );
    }
}
