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
        $fleetService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\FleetService');
        $logger = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\SimpleLoggerService');
        $emailService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\EmailService');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $exportConfig = $config['export'];
        $alertConfig = $config['alertSettings'];

        return new ExportRegistriesController(
            $customersService,
            $invoicesService,
            $fleetService,
            $emailService,
            $logger,
            $exportConfig,
            $alertConfig
        );
    }
}
