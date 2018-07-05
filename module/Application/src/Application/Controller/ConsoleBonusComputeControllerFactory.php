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
        $serverScriptService = $sharedServiceManager->get('\SharengoCore\Service\ServerScriptsService');
        $accountedTripsService = $sharedServiceManager->get('\SharengoCore\Service\AccountedTripsService');
        $carsService = $sharedServiceManager->get('\SharengoCore\Service\CarsService');
        $tripsService = $sharedServiceManager->get('\SharengoCore\Service\TripsService');
        $tripPaymentsService = $sharedServiceManager->get('\SharengoCore\Service\TripPaymentsService');
        $editTripsService = $sharedServiceManager->get('\SharengoCore\Service\EditTripsService');
        $bonusService = $sharedServiceManager->get('\SharengoCore\Service\BonusService');
        $fleetService = $sharedServiceManager->get('\SharengoCore\Service\FleetService');
        $poisService = $sharedServiceManager->get('\SharengoCore\Service\PoisService');
        $emailService = $sharedServiceManager->get('\SharengoCore\Service\EmailService');
        $zonesService = $sharedServiceManager->get('\SharengoCore\Service\ZonesService');
        $eventsService = $sharedServiceManager->get('\SharengoCore\Service\EventsService'); //MongoDB
        $customerPointForm = $sharedServiceManager->get('CustomerPointForm');
        $logger = $sharedServiceManager->get('\SharengoCore\Service\SimpleLoggerService');
        $config = $sharedServiceManager->get('\Configuration')['bonus']['zones'];
        $position = $sharedServiceManager->get('Config');
        $position = $position['position'];
        $pointConfig = $sharedServiceManager->get('Config');
        $carsBonusService = $sharedServiceManager->get('\SharengoCore\Service\CarsBonusService');
        

        return new ConsoleBonusComputeController(
            $customerService,
            $serverScriptService,
            $accountedTripsService,
            $carsService,
            $tripsService,
            $tripPaymentsService,
            $editTripsService,
            $bonusService,
            $zonesService,
            $emailService,
            $poisService,
            $eventsService,
            $logger,
            $config,
            $pointConfig,
            $customerPointForm,
            $fleetService,
            $position,
            $carsBonusService
        );
    }
}
