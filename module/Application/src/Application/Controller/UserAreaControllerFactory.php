<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Reflection;

class UserAreaControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $I_customerService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\CustomersService');
        $I_tripService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripsService');
        $userService = $serviceLocator->getServiceLocator()->get('zfcuser_auth_service');
        $invoicesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\Invoices');
        $profileForm = $serviceLocator->getServiceLocator()->get('ProfileForm');
        $passwordForm = $serviceLocator->getServiceLocator()->get('PasswordForm');
        $driverLicenseForm = $serviceLocator->getServiceLocator()->get('DriverLicenseForm');
        $promoCodeForm = $serviceLocator->getServiceLocator()->get('PromoCodeForm');
        $hydrator = new Reflection();
        $cartasiPaymentsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiPayments');
        $promoCodeService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\PromoCodesService');
        $tripPaymentsService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\TripPaymentsService');
        $cartasiContractsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiContracts');
        $bonusPackagesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\BonusPackagesService');


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
            $bonusPackagesService
        );
    }
}
