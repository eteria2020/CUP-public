<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use SharengoCore\Entity\Configurations;

class ConsoleControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        $reservationsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ReservationsService');
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $profilingPlatformService =  $serviceLocator->getServiceLocator()->get('ProfilingPlatformService');
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $accountTripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\AccountTripsService');

        $configurationService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ConfigurationsService');
        $alarmConfig = $configurationService->getConfigurationsKeyValueBySlug(Configurations::ALARM);
        $invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\Invoices');
        $cartasiTransactionsRepository = $entityManager->getRepository('\Cartasi\Entity\Transactions');
        $cartasiContractsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiContracts');
        $logger = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\SimpleLoggerService');
        $emailService = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\EmailService');
        $customerDeactivationService = $serviceLocator->getServiceLocator()->get('\SharengoCore\Service\CustomerDeactivationService');
        
        
        return new ConsoleController(
            $customerService,
            $cartasiContractsService,
            $logger,
            $emailService,
            $customerDeactivationService,
            $carsService,
            $reservationsService,
            $entityManager,
            $profilingPlatformService,
            $tripsService,
            $accountTripsService,
            $alarmConfig,
            $invoicesService,
            $cartasiTransactionsRepository
        );
    }
}
