<?php
namespace Application\View\Helper;


class LanguageMenu
{
    private $currentLanguage;
    private $languages;
    private $currentLanguageLabel;

    /**
     * LanguageMenu constructor.
     * @param $currentLanguageLabel
     * @param array $menuLanguages
     */
    public function __construct($currentLanguage, $currentLanguageLabel, array $menuLanguages)
    {
        $this->currentLanguage = $currentLanguage;
        $this->currentLanguageLabel = $currentLanguageLabel;
        $this->languages = $menuLanguages;
    }

    /**
     * @return mixed
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * @return string
     */
    public function getCurrentLanguageLabel()
    {
        return $this->currentLanguageLabel;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

}
