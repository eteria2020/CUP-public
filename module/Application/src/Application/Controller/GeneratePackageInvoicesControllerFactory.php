<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeneratePackageInvoicesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedManager = $serviceLocator->getServiceLocator();

        $packages = $sharedManager->get('SharengoCore\Service\CustomerBonusService');
        $logger = $sharedManager->get('SharengoCore\Service\SimpleLoggerService');

        return new GeneratePackageInvoicesController(
            $packages,
            $logger
        );
    }
}
