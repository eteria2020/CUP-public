<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProviderAuthenticatedCustomerRegisteredFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $providerAuthenticatedCustomersService = $serviceLocator->get('SharengoCore\Service\ProviderAuthenticatedCustomersService');

        return new ProviderAuthenticatedCustomerRegistered(
            $providerAuthenticatedCustomersService
        );
    }
}
