<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DriversLicenseValidationControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $enqueueValidationService = $sharedLocator->get('MvLabsDriversLicenseValidation\EnqueueValidation');
        $customerService = $sharedLocator->get('SharengoCore\Service\CustomersService');

        return new DriversLicenseValidationController(
            $enqueueValidationService,
            $customerService
        );
    }
}
