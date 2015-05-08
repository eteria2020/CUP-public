<?php

namespace Multilanguage\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Multilanguage\Service\LanguageService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translatorService = $serviceLocator->get('Translator');
        $localeService = $serviceLocator->get('DetectLocaleService');

        return new LanguageService($translatorService, $localeService);
    }
}
