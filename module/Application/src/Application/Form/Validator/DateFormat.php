<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class DateFormat extends AbstractValidator
{
    const INVALID_FORMAT = 'invalid format';

    protected $messageTemplates = [
        self::INVALID_FORMAT => "Il formato della data non è corretto"
    ];

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::INVALID_FORMAT] = $translator->translate("Il formato della data non è corretto");

        $this->setValue($value);

        if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[12][0-9]{3}$/", $value)) {
            $this->error(self::INVALID_FORMAT);
            return false;
        }

        return true;
    }
}
