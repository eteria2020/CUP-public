<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LandingPageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersBonusPackagesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\BonusPackagesService');

        return new LandingPageController(
            $customersBonusPackagesService
        );
    }
}
