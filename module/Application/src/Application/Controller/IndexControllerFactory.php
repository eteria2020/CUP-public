<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerfactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $mobileUrl = $config['mobile']['url'];
        $zoneService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ZonesService');
        $carsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CarsService');
        $fleetService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\FleetService');
        $poisService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PoisService');
        $customersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $cartasiContractsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiContracts');
        $registrationService = $serviceLocator->getServiceLocator()->get('RegistrationService');
        $tripPaymentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripPaymentsService');
        $paymentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PaymentsService');
        $paymentScriptRunService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PaymentScriptRunsService');
        $tripsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $extraPaymentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ExtraPaymentsService');
        $bonusService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\BonusService');
        $customersBonusPackagesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\BonusPackagesService');
        $buyCustomerBonusPackage = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\BuyCustomerBonusPackage');
        $documentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\DocumentsService');

        return new IndexController(
            $config,
            $mobileUrl,
            $zoneService,
            $carsService,
            $fleetService,
            $poisService,
            $customersService,
            $cartasiContractsService,
            $registrationService,
            $tripPaymentsService,
            $paymentScriptRunService,
            $paymentsService,
            $tripsService,
            $extraPaymentsService,
            $bonusService,
            $customersBonusPackagesService,
            $buyCustomerBonusPackage,
            $documentsService
        );
    }
}
