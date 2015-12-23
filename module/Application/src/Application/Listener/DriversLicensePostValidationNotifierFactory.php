<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DriversLicensePostValidationNotifierFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $emailService = $serviceLocator->get('SharengoCore\Service\EmailService');
        $emailSettings = $serviceLocator->get('Config')['emailSettings'];

        return new DriversLicensePostValidationNotifier($emailService, $emailSettings);
    }
}
