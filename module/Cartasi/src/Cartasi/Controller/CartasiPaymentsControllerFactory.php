<?php

namespace Cartasi\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CartasiPaymentsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cartasiPaymentsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiPaymentsService');
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $cartasiConfig = $config['cartasi'];
        $url = $serviceLocator->getServiceLocator()->get('ViewHelperManager')->get('Url');

        return new CartasiPaymentsController(
            $cartasiPaymentsService,
            $customersService,
            $config['cartasi'],
            $url
        );
    }
}
