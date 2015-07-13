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
        $promoCodeFieldset = new PromoCodeFieldset($translator, $promoCodeService);

        return new PromoCodeForm($promoCodeFieldset);
    }
}