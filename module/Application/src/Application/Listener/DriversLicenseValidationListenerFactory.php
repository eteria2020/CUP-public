<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DriversLicenseValidationListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $enqueueValidation = $serviceLocator->get('MvLabsDriversLicenseValidation\EnqueueValidation');

        return new DriversLicenseValidationListener($enqueueValidation);
    }
}
