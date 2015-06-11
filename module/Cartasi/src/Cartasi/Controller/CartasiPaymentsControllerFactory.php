<?php

namespace Cartasi\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CartasiPaymentsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cartasiPaymentsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiPaymentsService');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $cartasiConfig = $config['cartasi'];

        return new CartasiPaymentsController($cartasiPaymentsService, $cartasiConfig);
    }
}
