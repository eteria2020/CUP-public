<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProviderAuthenticationServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('ScnSocialAuth-ModuleOptions');
        $hybridAuth = $serviceLocator->get('HybridAuth');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $emailService = $serviceLocator->get('SharengoCore\Service\EmailService');
        $viewHelperManager = $serviceLocator->get('viewHelperManager');

        return new ProviderAuthenticationService(
            $moduleOptions,
            $hybridAuth,
            $entityManager,
            $emailService,
            $viewHelperManager
        );
    }
}
