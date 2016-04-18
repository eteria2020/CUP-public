<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

class ProviderAuthenticationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('ScnSocialAuth-ModuleOptions');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $emailService = $serviceLocator->get('SharengoCore\Service\EmailService');
        $viewHelperManager = $serviceLocator->get('viewHelperManager');

        try {
            $hybridAuth = $serviceLocator->get('HybridAuth');
        } catch (ServiceNotCreatedException $e) {
            // This is likely the user cancelling login...
            $hybridAuth = new FailedHybridAuth();
        }

        return new ProviderAuthenticationService(
            $moduleOptions,
            $entityManager,
            $emailService,
            $viewHelperManager,
            $hybridAuth
        );
    }
}
