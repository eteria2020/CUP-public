<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class DateFromToday extends AbstractValidator
{
    const FROMTODAY = 'dateFromToday';

    protected $messageTemplates = [
        self::FROMTODAY => "La patente non può essere scaduta"
    ];

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::FROMTODAY] = $translator->translate("La patente non può essere scaduta");

        $this->setValue($value);

        $date = date_create($value);
        
        $currentDate = date_create();

        if ($date < $currentDate) {
            $this->error(self::FROMTODAY);
            return false;
        }
        

        return true;
    }
}
