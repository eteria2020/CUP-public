<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleComputePayInvoiceControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $tripsService = $sharedServiceManager->get('SharengoCore\Service\TripsService');
        $tripCostService = $sharedServiceManager->get('SharengoCore\Service\TripCostService');
        $tripPaymentsService = $sharedServiceManager->get('SharengoCore\Service\TripPaymentsService');
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $invoicesService = $sharedServiceManager->get('SharengoCore\Service\Invoices');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');

        return new ConsoleComputePayInvoiceController(
            $tripsService,
            $tripCostService,
            $tripPaymentsService,
            $paymentsService,
            $invoicesService,
            $logger
        );
    }
}
