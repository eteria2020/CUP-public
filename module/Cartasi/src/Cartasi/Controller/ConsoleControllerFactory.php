<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\InvoicesService');

        return new ConsoleController($customersService, $invoicesService);
    }
}
