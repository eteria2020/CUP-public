<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GenerateTripInvoiceControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $tripPaymentsService = $sharedLocator->get('SharengoCore\Service\TripPaymentsService');
        $invoicesService = $sharedLocator->get('SharengoCore\Service\Invoices');

        return new GenerateTripInvoiceController(
            $tripPaymentsService,
            $invoicesService
        );
    }
}
