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
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $invoicesService = $sharedServiceManager->get('SharengoCore\Service\Invoices');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');

        return new ConsolePayInvoiceController(
            $tripPaymentsService,
            $paymentsService,
            $invoicesService,
            $logger
        );
    }
}