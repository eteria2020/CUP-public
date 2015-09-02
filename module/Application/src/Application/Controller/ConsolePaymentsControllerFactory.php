<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsolePaymentsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $tripPaymentsService = $sharedServiceManager->get('SharengoCore\Service\TripPaymentsService');
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $customersService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');

        return new ConsolePaymentsController(
            $tripPaymentsService,
            $paymentsService,
            $customersService,
            $logger
        );
    }
}
