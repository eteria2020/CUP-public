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
        $profileForm = $serviceLocator->getServiceLocator()->get('ProfileForm');
        $passwordForm = $serviceLocator->getServiceLocator()->get('PasswordForm');
        $driverLicenseForm = $serviceLocator->getServiceLocator()->get('DriverLicenseForm');
        $hydrator = new Reflection();
        $cartasiPaymentsService = $serviceLocator->getServiceLocator()->get('Cartasi\Service\CartasiPayments');

        return new UserAreaController(
            $I_customerService,
            $I_tripService,
            $userService,
            $profileForm,
            $passwordForm,
            $driverLicenseForm,
            $hydrator,
            $cartasiPaymentsService
        );
    }
}
