<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Reflection;

class UserAreaControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $I_customerService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $I_tripService = $sharedLocator->get('SharengoCore\Service\TripsService');
        $translator = $serviceLocator->getServiceLocator()->get('Translator');
        $userService = $sharedLocator->get('zfcuser_auth_service');
        $invoicesService = $sharedLocator->get('SharengoCore\Service\Invoices');
        $profileForm = $sharedLocator->get('ProfileForm');
        $passwordForm = $sharedLocator->get('PasswordForm');
        $mobileForm = $sharedLocator->get('MobileForm');
        $driverLicenseForm = $sharedLocator->get('DriverLicenseForm');
        $hydrator = new Reflection();
        $cartasiPaymentsService = $sharedLocator->get('Cartasi\Service\CartasiPayments');
        $tripPaymentsService = $sharedLocator->get('SharengoCore\Service\TripPaymentsService');
        $cartasiContractsService = $sharedLocator->get('Cartasi\Service\CartasiContracts');
        $bannerJsonpUrl = $sharedLocator->get('Configuration')['banner-jsonp'];
        $disableContractService = $sharedLocator->get('SharengoCore\Service\DisableContractService');
        $paymentScriptRunService = $sharedLocator->get('SharengoCore\Service\PaymentScriptRunsService');
        $paymentService = $sharedLocator->get('SharengoCore\Service\PaymentsService');
        $customerDeactivationService = $sharedLocator->get('SharengoCore\Service\CustomerDeactivationService');
        $extraPaymentsService = $sharedLocator->get('SharengoCore\Service\ExtraPaymentsService');

        return new UserAreaController(
            $I_customerService,
            $I_tripService,
            $translator,
            $userService,
            $invoicesService,
            $profileForm,
            $passwordForm,
            $mobileForm,
            $driverLicenseForm,
            $hydrator,
            $cartasiPaymentsService,
            $tripPaymentsService,
            $cartasiContractsService,
            $bannerJsonpUrl,
            $disableContractService,
            $paymentScriptRunService,
            $paymentService,
            $customerDeactivationService,
            $extraPaymentsService
        );
    }

}
