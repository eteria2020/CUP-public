<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProfilingPlatformServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Application\Service\RegistrationService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
        $profilingPlatformSettings = $serviceLocator->get('Configuration')['profiling-platform'];

        return new ProfilingPlaformService(
            $profilingPlatformSettings
        );
    }
}
