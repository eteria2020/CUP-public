<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class BirthProvince extends AbstractValidator
{
    const BIRTH_ABROAD = 'BirthAbroad';
    const BIRTH_ITALY = 'BirthItaly';

    protected $messageTemplates = [
        self::BIRTH_ABROAD => "E' necessario avere Estero come Provincia di nascita",
        self::BIRTH_ITALY => "E' necessario avere una Provincia di nascita italiana"
    ];

    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        //country Estera e Provincia di nascita non estero
        if ($context['birthCountry'] != 'it' && $value != 'EE') {
            $this->error(self::BIRTH_ABROAD);
            return false;
        }
        //country Italia e Provincia di nascita estero
        if ($context['birthCountry'] == 'it' && $value == 'EE') {
            $this->error(self::BIRTH_ITALY);
            return false;
        }

        return true;
    }
}
