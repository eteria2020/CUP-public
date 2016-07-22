<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PromoCodeFormFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PromoCodeForm
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('Translator');
        $promoCodeService = $serviceLocator->get('SharengoCore\Service\PromoCodesService');
        $carrefourService = $serviceLocator->get('SharengoCore\Service\CarrefourService');
        $promoCodeFieldset = new PromoCodeFieldset(
            $translator,
            $promoCodeService,
            $carrefourService
        );

        return new PromoCodeForm($promoCodeFieldset);
    }
}
