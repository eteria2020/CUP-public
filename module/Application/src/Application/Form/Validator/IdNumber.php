<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class IdNumber extends AbstractValidator
{
    const INVALID = 'IdNumber';

    protected $messageTemplates = [
        self::INVALID => "Il numero identificativo non è corretto"
    ];

    private $length;

    public function __construct($options)
    {
        parent::__construct();
        $this->length = $options["length"];
    }

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::INVALID] = $translator->translate("Il numero identificativo non è corretto");

        $value = strtoupper($value);
        $this->setValue($value);

        if (!preg_match("/^([0-9]{".$this->length."})$/i", $value)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
