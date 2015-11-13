<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\I18n\Translator;

class LongLanguage extends AbstractHelper
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $lang
     * @return string
     */
    public function __invoke($lang)
    {
        $longLanguages = [
            "it" => $this->translator->translate("Italiano"),
            "de" => $this->translator->translate("tedesco"),
            "fr" => $this->translator->translate("francese"),
            "es" => $this->translator->translate("spagnolo"),
            "en" => $this->translator->translate("inglese"),
            "ch" => $this->translator->translate("cinese"),
            "ru" => $this->translator->translate("russo"),
            "pt" => $this->translator->translate("portoghese")
        ];

        return isset($longLanguages[$lang]) ? $longLanguages[$lang] : 'Italiano';
    }
}
