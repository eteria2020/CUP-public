<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class OptionAccepted extends AbstractValidator
{
    const INVALID = 'mustAccept';

    protected $messageTemplates = [
        self::INVALID => "E' necessario accettare le condizioni"
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        // we suppose that the correct option to check has id 0
        if ($value !== "0") {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
