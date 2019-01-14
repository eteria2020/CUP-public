<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class NotFutureDate extends AbstractValidator
{
    const NOT_FUTURE = 'oneYearDate';

    protected $messageTemplates = [
        self::NOT_FUTURE => "La data della patente non può essere futura alla data odierna"
    ];

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::NOT_FUTURE] = $translator->translate("La data della patente non può essere futura alla data odierna");

        $this->setValue($value);

        $date = date_create($value);

        if ($date > date_create()) {
            $this->error(self::NOT_FUTURE);
            return false;
        }

        return true;
    }
}
