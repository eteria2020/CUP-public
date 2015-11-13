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
     * @return ProfilingPlaformService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $profilingPlatformSettings = $serviceLocator->get('Configuration')['profiling-platform'];
        $fleetService = $serviceLocator->get('SharengoCore\Service\FleetService');

        return new ProfilingPlaformService(
            $profilingPlatformSettings,
            $fleetService
        );
    }
}
