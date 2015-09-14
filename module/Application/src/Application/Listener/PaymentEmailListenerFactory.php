<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentEmailListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $emailService = $serviceLocator->get('SharengoCore\Service\EmailService');
        $url = $serviceLocator->get('Configuration')['website']['uri'];

        return new PaymentEmailListener($emailService, $url);
    }
}
