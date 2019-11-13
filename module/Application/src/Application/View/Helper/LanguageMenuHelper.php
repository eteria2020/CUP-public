<?php
namespace Application\View\Helper;

use Application\Listener\ChangeLanguageDetector;
use MvLabsMultilanguage\Service\LanguageService;
use Zend\View\Helper\AbstractHelper;

class LanguageMenuHelper extends AbstractHelper
{
    private $config;
    private $serverInstance;
    private $languages;
    private $languageService;

    /**
     * LanguageMenuHelper constructor.
     * @param array $config
     * @param array $languages
     * @param LanguageService $languageService
     */
    public function __construct(array $config, array $languages, LanguageService $languageService)
    {
        $this->config = $config;
        $this->languages = $languages;
        $this->languageService = $languageService;

        $this->serverInstance["id"] = "it_IT";
        if(isset($this->config['serverInstance'])) {
            $this->serverInstance = $this->config['serverInstance'];
        }
    }

    /**
     * @return LanguageMenu
     */
    public function __invoke()
    {
        $currentLocale = $this->languageService->getTranslator()->getLocale();
        $currentLabel = '';
        $menuLanguages = [];
        foreach ($this->languages as $language) {
            $locale = $language['locale'];
            $label = $language['label'];
            $url = "?" . ChangeLanguageDetector::URL_PARAM . "=" . $locale;

            if ($locale == $currentLocale) {
                $currentLabel = $label;
                $currentLanguage = [
                    'code' => $locale,
                    'label' => $label,
                    'url' => $url
                ];

            } else if ($locale == 'en_US' || $locale ==  $this->serverInstance["id"] ) {
                $menuLanguages[] = [
                    'code' => $locale,
                    'label' => $label,
                    'url' => $url
                ];
            }
        }

        return new LanguageMenu($currentLanguage, $currentLabel, $menuLanguages);
    }
}
