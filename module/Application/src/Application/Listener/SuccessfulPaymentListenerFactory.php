<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SuccessfulPaymentListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $deactivationService = $serviceLocator->get('SharengoCore\Service\CustomerDeactivationService');
        $customersService = $serviceLocator->get('SharengoCore\Service\CustomersService');
        $paymentsService = $serviceLocator->get('PaymentService');

        return new SuccessfulPaymentListener(
            $deactivationService,
            $customersService,
            $paymentsService
        );
    }
}
