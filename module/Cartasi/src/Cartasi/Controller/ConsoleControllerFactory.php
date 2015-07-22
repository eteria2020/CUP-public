<?php

namespace Cartasi\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $invoicesService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\Invoices');

        return new ConsoleController($customersService, $invoicesService);
    }
}
