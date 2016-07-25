<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Reflection;

class AdditionalServicesControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedLocator = $serviceLocator->getServiceLocator();

        $customersService = $sharedLocator->get('SharengoCore\Service\CustomersService');
        $carrefourService = $sharedLocator->get('SharengoCore\Service\CarrefourService');
        $promoCodeForm = $sharedLocator->get('PromoCodeForm');
        $promoCodeService = $sharedLocator->get('SharengoCore\Service\PromoCodesService');
        $bonusPackagesService = $sharedLocator->get('SharengoCore\Service\BonusPackagesService');
        $authService = $sharedLocator->get('zfcuser_auth_service');

        return new AdditionalServicesController(
            $customersService,
            $carrefourService,
            $promoCodeForm,
            $promoCodeService,
            $bonusPackagesService,
            $authService
        );
    }
}
