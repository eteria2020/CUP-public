<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerBonusPackagesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersBonusPackagesService = $sharedLocator->get('SharengoCore\Service\BonusPackagesService');
        $buyCustomerBonusPackage = $sharedLocator->get('SharengoCore\Service\BuyCustomerBonusPackage');
        $cartasiContractsService = $sharedLocator->get('Cartasi\Service\CartasiContracts');
        $translationService = $serviceLocator->getServiceLocator()->get('Translator');

        return new CustomerBonusPackagesController(
            $customersBonusPackagesService,
            $buyCustomerBonusPackage,
            $cartasiContractsService,
            $translationService
        );
    }
}
