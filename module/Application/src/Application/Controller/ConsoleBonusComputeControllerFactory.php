<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleBonusComputeControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $customerService = $sharedServiceManager->get('SharengoCore\Service\CustomersService');
        $accountTripsService = $sharedServiceManager->get('SharengoCore\Service\AccountTripsService');
        $tripsService = $sharedServiceManager->get('SharengoCore\Service\TripsService');
        //$tripCostService = $sharedServiceManager->get('SharengoCore\Service\TripCostService');
        $bonusService = $sharedServiceManager->get('SharengoCore\Service\BonusService');
        $eventsService = $sharedServiceManager->get('SharengoCore\Service\EventsService'); //MongoDB
        
        $logger = $sharedServiceManager->get('SharengoCore\Service\SimpleLoggerService');
        
        //$carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        //$entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        //$accountTripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\AccountTripsService');

        //$invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\Invoices');


        return new ConsoleBonusComputeController(
            $customerService,
            $accountTripsService,
            $tripsService,
            //$tripCostService,
            $bonusService,
            $eventsService,
            $logger
        );
    }
}
