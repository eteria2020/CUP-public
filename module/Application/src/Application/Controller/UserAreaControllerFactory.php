<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Reflection;

class UserAreaControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $I_customerService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $I_tripService = $sharedLocator->get('SharengoCore\Service\TripsService');
        $userService = $sharedLocator->get('zfcuser_auth_service');
        $invoicesService = $sharedLocator->get('SharengoCore\Service\Invoices');
        $profileForm = $sharedLocator->get('ProfileForm');
        $passwordForm = $sharedLocator->get('PasswordForm');
        $driverLicenseForm = $sharedLocator->get('DriverLicenseForm');
        $hydrator = new Reflection();
        $cartasiPaymentsService = $sharedLocator->get('Cartasi\Service\CartasiPayments');
        $tripPaymentsService = $sharedLocator->get('SharengoCore\Service\TripPaymentsService');
        $cartasiContractsService = $sharedLocator->get('Cartasi\Service\CartasiContracts');
        $bannerJsonpUrl = $sharedLocator->get('Configuration')['banner-jsonp'];
        $discounterUrl = $sharedLocator->get('Configuration')['discounterSite']['url'];

        return new UserAreaController(
            $I_customerService,
            $I_tripService,
            $userService,
            $invoicesService,
            $profileForm,
            $passwordForm,
            $driverLicenseForm,
            $hydrator,
            $cartasiPaymentsService,
            $tripPaymentsService,
            $cartasiContractsService,
            $bannerJsonpUrl,
            $discounterUrl
        );
    }
}
