<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $paypalRequest = $serviceLocator->getServiceLocator()->get('PaypalRequest');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $paypalConfig = $config['paypal'];
        $sharengoConfig = $config['sharengo'];

        return new PaymentController(
            $customerService,
            $paypalRequest,
            $paypalConfig,
            $sharengoConfig
        );
    }
}
