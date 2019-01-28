<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class IdNumber extends AbstractValidator
{
    const INVALID = 'IdNumber';

    protected $messageTemplates = [
        self::INVALID => "Il numero identificativo non è corretto"
    ];

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::INVALID] = $translator->translate("Il numero identificativo non è corretto");

        $value = strtoupper($value);
        $this->setValue($value);

        if (!preg_match("/^([0-9]{10})$/i", $value)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
