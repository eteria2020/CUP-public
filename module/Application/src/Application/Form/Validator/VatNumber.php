<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class VatNumber extends AbstractValidator
{
    const SPACE = 'noSppaces';
    const NUMERIC = 'numeric';
    const LENGTH = 'length';

    protected $messageTemplates = [
        self::SPACE => "La partita IVA non può contenere spazi",
        self::NUMERIC => "La partita IVA deve essere numerica, al più preceduta dal prefisso IT",
        self::LENGTH => "La partita IVA deve essere di 11 cifre, al più preceduta dal prefisso IT"
    ];

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::SPACE] = $translator->translate("La partita IVA non può contenere spazi");
        $messageTemplates[ self::NUMERIC] = $translator->translate("La partita IVA deve essere numerica, al più preceduta dal prefisso IT");
        $messageTemplates[ self::LENGTH] = $translator->translate("La partita IVA deve essere di 11 cifre, al più preceduta dal prefisso IT");

        $this->setValue($value);

        if (strpos($value, ' ') !== false) {
            $this->error(self::SPACE);
            return false;
        }

        if (!(
            strlen(str_replace('IT', '', strtoupper($value))) === 11 ||
            (strlen($value) === 13 && strtoupper(substr($value, 0, 2)) === 'IT')
        )) {
            $this->error(self::LENGTH);
            return false;
        }

        $value = str_replace('IT', '', strtoupper($value));

        if (!preg_match("/^[0-9]+$/", $value)) {
            $this->error(self::NUMERIC);
            return false;
        }

        return true;
    }
}
