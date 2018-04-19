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
        $extraPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ExtraPaymentsService');
        $invoicesService = $sharedServiceManager->get('SharengoCore\Service\Invoices');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');
        $processPaymentsService = $sharedServiceManager->get('SharengoCore\Service\ProcessPaymentsService');
        $entityManager = $sharedServiceManager->get('doctrine.entitymanager.orm_default');
        $paymentScriptRunService = $sharedServiceManager->get('SharengoCore\Service\PaymentScriptRunsService');
        $extraScriptRunService = $sharedServiceManager->get('SharengoCore\Service\PxtraScriptRunsService');

        return new ConsolePayInvoiceController(
            $tripPaymentsService,
            $extraPaymentsService,
            $invoicesService,
            $logger,
            $processPaymentsService,
            $entityManager,
            $paymentScriptRunService,
            $extraScriptRunService
        );
    }
}
