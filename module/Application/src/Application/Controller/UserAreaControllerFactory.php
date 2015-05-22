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
        $userService = $serviceLocator->getServiceLocator()->get('zfcuser_auth_service');
        $profileForm = $serviceLocator->getServiceLocator()->get('ProfileForm');
        $passwordForm = $serviceLocator->getServiceLocator()->get('PasswordForm');
        $hydrator = new Reflection();

        return new UserAreaController(
            $I_customerService,
            $userService,
            $profileForm,
            $passwordForm,
            $hydrator
        );
    }
}
