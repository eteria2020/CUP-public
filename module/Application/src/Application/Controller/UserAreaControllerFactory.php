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
        $promoCodeForm = $sharedLocator->get('PromoCodeForm');
        $hydrator = new Reflection();
        $cartasiPaymentsService = $sharedLocator->get('Cartasi\Service\CartasiPayments');
        $promoCodeService = $sharedLocator->get('SharengoCore\Service\PromoCodesService');
        $tripPaymentsService = $sharedLocator->get('SharengoCore\Service\TripPaymentsService');
        $cartasiContractsService = $sharedLocator->get('Cartasi\Service\CartasiContracts');
        $bonusPackagesService = $sharedLocator->get('SharengoCore\Service\BonusPackagesService');
        $bannerJsonpUrl = $sharedLocator->get('Configuration')['bannerJsonp'];

        return new UserAreaController(
            $I_customerService,
            $I_tripService,
            $userService,
            $invoicesService,
            $profileForm,
            $passwordForm,
            $driverLicenseForm,
            $promoCodeForm,
            $hydrator,
            $cartasiPaymentsService,
            $promoCodeService,
            $tripPaymentsService,
            $cartasiContractsService,
            $bonusPackagesService,
            $bannerJsonpUrl
        );
    }
}
