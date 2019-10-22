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
            "en" => $this->translator->translate("inglese"),
            "sk" => $this->translator->translate("slovacco"),
            "nl" => $this->translator->translate("olandese"),
            "sl" => $this->translator->translate("sloveno")
        ];

        return isset($longLanguages[$lang]) ? $longLanguages[$lang] : 'Italiano';
    }
}
