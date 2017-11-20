<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsolePaymentsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $tripPaymentsService = $sharedServiceManager->get('SharengoCore\Service\TripPaymentsService');
        $paymentsService = $sharedServiceManager->get('SharengoCore\Service\PaymentsService');
        $customersService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $tripsService = $sharedServiceManager->get('SharengoCore\Service\TripsService');
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');
        $cartasiTransactionsRepository =  $entityManager->getRepository('\Cartasi\Entity\Transactions');
        return new ConsolePaymentsController(
            $entityManager,
            $tripPaymentsService,
            $paymentsService,
            $customersService,
            $tripsService,
            $logger,
            $cartasiTransactionsRepository
        );
    }
}
