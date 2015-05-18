<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Sendmail;

class PaymentServiceFactory implements FactoryInterface
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

        $translationService = $serviceLocator->get('Translator');

        return new PaymentService(
            $emailTransport,
            $emailSettings,
            $translationService
        );
    }
}