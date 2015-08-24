<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
   	    $config = $serviceLocator->getServiceLocator()->get('Config');
   	    $alarmConfig = $config['alarm'];
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
