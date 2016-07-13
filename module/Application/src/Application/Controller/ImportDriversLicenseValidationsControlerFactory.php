<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImportDriversLicenseValidationsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $validationService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\DriversLicenseValidationService');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $exportConfig = $config['export'];

        return new ImportDriversLicenseValidationsController(
            $customersService,
            $validationService,
            $logger
        );
    }
}
