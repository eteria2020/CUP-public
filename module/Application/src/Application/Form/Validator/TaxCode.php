<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class TaxCode extends AbstractValidator
{
    const INVALID = 'taxCode';

    protected $messageTemplates = [
        self::INVALID => "Il codice fiscale non è corretto"
    ];

    public function isValid($value)
    {
        $value = strtoupper($value);
        $this->setValue($value);

        if (!preg_match("/^([a-z]{3})([a-z]{3})([0-9]{2})([abcdehlmprst]{1})([0-9]{2})([a-z]{1}[0-9lmnpqrstuv]{3})([a-z]{1})$/i", $value)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
