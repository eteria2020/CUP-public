<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class Password extends AbstractValidator
{
    const LENGTH = 'length';
    const UPPER  = 'upper';
    const LOWER  = 'lower';
    const DIGIT  = 'digit';

    protected $messageTemplates = array(
        self::LENGTH => "Deve essere lunga almeno 8 caratteri",
        self::UPPER  => "Deve contenere almeno una maiuscola",
        self::LOWER  => "Deve contenere almeno una minuscola",
        self::DIGIT  => "Deve contenere almeno un numero"
    );

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::LENGTH] = $translator->translate("Deve essere lunga almeno 8 caratteri");
        $messageTemplates[ self::UPPER] = $translator->translate("Deve contenere almeno una maiuscola");
        $messageTemplates[ self::LOWER] = $translator->translate("Deve contenere almeno una minuscola");
        $messageTemplates[ self::DIGIT] = $translator->translate("Deve contenere almeno un numero");

        $this->setValue($value);

        $isValid = true;

        if (strlen($value) < 8) {
            $this->error(self::LENGTH);
            $isValid = false;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->error(self::UPPER);
            $isValid = false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->error(self::LOWER);
            $isValid = false;
        }

        if (!preg_match('/\d/', $value)) {
            $this->error(self::DIGIT);
            $isValid = false;
        }

        return $isValid;
    }
}
