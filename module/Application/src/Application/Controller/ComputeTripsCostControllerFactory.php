<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ComputeTripsCostControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $tripCostService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripCostService');
        $tripPaymentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripPaymentsService');
        $invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\Invoices');
        $entityManager = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        return new ComputeTripsCostController(
            $tripsService,
            $tripCostService,
            $tripPaymentsService,
            $invoicesService,
            $entityManager);
    }
}
