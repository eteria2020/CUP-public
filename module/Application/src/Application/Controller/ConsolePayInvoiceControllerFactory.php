<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsolePayInvoiceControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $tripPaymentsService = $sharedServiceManager->get('SharengoCore\Service\TripPaymentsService');
        $invoicesService = $sharedServiceManager->get('SharengoCore\Service\Invoices');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');
        $processPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ProcessPaymentsService');

        return new ConsolePayInvoiceController(
            $tripPaymentsService,
            $invoicesService,
            $logger,
            $processPaymentsService
        );
    }
}
