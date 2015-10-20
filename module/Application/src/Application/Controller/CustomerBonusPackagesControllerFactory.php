<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerBonusPackagesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $customersBonusPackagesService = $serviceLocator->getServiceLocator()->get('SharengoCore\Service\BonusPackagesService');

        return new CustomerBonusPackagesController($customersBonusPackagesService);
    }
}
