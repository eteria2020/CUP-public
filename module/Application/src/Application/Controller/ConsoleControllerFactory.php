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

        return new ConsoleController(
            $customerService,
            $carsService,
            $reservationsService,
            $entityManager,
            $profilingPlatformService,
            $tripsService,
            $accountTripsService,
            $alarmConfig,
            $invoicesService
        );
    }
}
