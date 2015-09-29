<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExportRegistriesControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\Invoices');
        $logger = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SimpleLoggerService');

        return new ExportRegistriesController(
            $customersService,
            $invoicesService,
            $logger
        );
    }
}
