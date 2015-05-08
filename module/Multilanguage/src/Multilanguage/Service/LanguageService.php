<?php

namespace Multilanguage\Service;

use Zend\Mvc\I18n\Translator;

class LanguageService
{
    /**
     * @var Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * @var Multilanguage\Service\DetectLocaleService
     */
    private $localeService;

    public function __construct(Translator $translator, DetectLocaleService $localeService)
    {
        $this->translator = $translator;
        $this->localeService = $localeService;
    }

    /**
     * select the appropriate locale to be used according to the received request
     * 
     * @param Zend\Http\PhpEnvironment\Request
     */
    public function setLocaleFromRequest($request)
    {
        $locale = $this->localeService->getLocaleFromUri($request->getUri());

        if (!$locale) {
            $this->localeService->getLocaleFromHeaders($request->getHeaders());
        }

        if ($locale) {
            $this->translator->setLocale($locale);
        }
    }

    /**
     * returns the used instance of the translator
     * 
     * @return Zend\Mvc\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * retrieves and returns the language used by the application form the
     * used locale
     * 
     * @return string two-characters format
     */
    public function getLanguage()
    {
        $locale = $this->translator->getLocale();
        return strtok($locale, '_');
    }
}
