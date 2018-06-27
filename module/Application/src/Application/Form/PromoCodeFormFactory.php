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
        $promoCodeOnceService = $serviceLocator->get('SharengoCore\Service\PromoCodesOnceService');
        $carrefourService = $serviceLocator->get('SharengoCore\Service\CarrefourService');
        $promoCodesMemberGetMemberService = $serviceLocator->get('SharengoCore\Service\PromoCodesMemberGetMemberService');

        $promoCodeFieldset = new PromoCodeFieldset(
            $translator,
            $promoCodeService,
            $promoCodeOnceService,
            $carrefourService,
            $promoCodesMemberGetMemberService
        );

        return new PromoCodeForm($promoCodeFieldset);
    }
}
