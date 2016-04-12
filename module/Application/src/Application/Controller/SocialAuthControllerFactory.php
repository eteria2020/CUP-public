<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SocialAuthControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $providerAuthentication = $serviceLocator->getServiceLocator()->get('Application\Service\ProviderAuthentication');
        $providerAuthenticatedCustomersService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\ProviderAuthenticatedCustomersService');

        return new SocialAuthController(
            $providerAuthentication,
            $providerAuthenticatedCustomersService
        );
    }
}
