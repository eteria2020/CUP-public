<?php

namespace Multilanguage\Service;

class DetectLocaleService
{
    /**
     * @var
     */
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * tries to detect to correct locale from the request uri
     * if it is not able to do so, it returns null
     * 
     * @param Zend\Uri\Http
     * @return string|null
     */
    public function getLocaleFromUri($uri)
    {
        //detects language from the first segment of the path
        if (preg_match('/[^\/]+/', $uri->getPath(), $matches)) {
            $lang = $matches[0];
            return $this->getLocaleFromLanguage($lang);
        }
    }

    public function getLocaleFromHeaders($headers)
    {
        $match = null;

        if ($headers->has('Accept-Language')) {
            $locales = $headers->get('Accept-Language')->getPrioritized();

            foreach ($locales as $locale) {
                $language = explode("-", $locale->getLanguage());
                $match = $this->getLocaleFromLanguage($language[0]);

                if ($match) {
                    return $match;
                }
            }
        }
    }

    /**
     * returns the first locale in the configuration that matches the given
     * language
     * if no match is found, it returns null
     * 
     * @param string with 2 characters
     * @return string|null
     */
    private function getLocaleFromLanguage($lang)
    {
        $allowedLocales = $this->config['multilanguage']['allowed_locales'];

        foreach ($allowedLocales as $locale) {
            if ($lang == substr($locale, 0, 2)) {
                return $locale;
            }
        }
    }
}
