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
        $carsService = $sharedServiceManager->get('\SharengoCore\Service\CarsService');
        $tripsService = $sharedServiceManager->get('\SharengoCore\Service\TripsService');
        $tripPaymentsService = $sharedServiceManager->get('\SharengoCore\Service\TripPaymentsService');
        $editTripsService = $sharedServiceManager->get('\SharengoCore\Service\EditTripsService');
        $bonusService = $sharedServiceManager->get('\SharengoCore\Service\BonusService');
        $poisService = $sharedServiceManager->get('\SharengoCore\Service\PoisService');
        $emailService = $sharedServiceManager->get('\SharengoCore\Service\EmailService');
        $zonesService = $sharedServiceManager->get('\SharengoCore\Service\ZonesService');
        $eventsService = $sharedServiceManager->get('\SharengoCore\Service\EventsService'); //MongoDB
        $customerPointForm = $sharedServiceManager->get('CustomerPointForm');
        $logger = $sharedServiceManager->get('\SharengoCore\Service\SimpleLoggerService');
        $config = $sharedServiceManager->get('\Configuration')['bonus']['zones'];
        $config = $sharedServiceManager->get('Config');

        return new ConsoleBonusComputeController(
            $customerService,
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
            $customerPointForm
        );
    }
}
