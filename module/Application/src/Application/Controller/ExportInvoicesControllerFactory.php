<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExportInvoicesControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $logger = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SimpleLoggerService');

        return new ExportInvoicesController(
            $entityManager,
            $customersService,
            $tripsService,
            $logger
        );
    }
}
