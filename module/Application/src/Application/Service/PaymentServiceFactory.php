<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Sendmail;

class RegistrationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Application\Service\PaymentService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $emailTransport = new Sendmail();
        $emailSettings = $serviceLocator->get('Configuration')['emailSettings'];

        return new RegistrationService(
            $emailTransport,
            $emailSettings
        );
    }
}