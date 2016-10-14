<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleBonusComputeControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();

        $customerService = $sharedServiceManager->get('\SharengoCore\Service\CustomersService');
        $tripsService = $sharedServiceManager->get('\SharengoCore\Service\TripsService');
        $editTripsService = $sharedServiceManager->get('\SharengoCore\Service\EditTripsService');
        $bonusService = $sharedServiceManager->get('\SharengoCore\Service\BonusService');
        $zonesService = $sharedServiceManager->get('\SharengoCore\Service\ZonesService');
        $eventsService = $sharedServiceManager->get('\SharengoCore\Service\EventsService'); //MongoDB

        $logger = $sharedServiceManager->get('\SharengoCore\Service\SimpleLoggerService');
        $config = $sharedServiceManager->get('\Configuration')['bonus']['zones'];

        return new ConsoleBonusComputeController(
            $customerService,
            $tripsService,
            $editTripsService,
            $bonusService,
            $zonesService,
            $eventsService,
            $logger,
            $config
        );
    }
}
