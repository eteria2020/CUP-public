<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class ZipCode extends AbstractValidator
{
    const INVALID = 'IdNumber';

    private $country;

    protected $messageTemplates = [
        self::INVALID => "Codice Avviamento Postale non corretto"
    ];

    public function __construct($options)
    {
        parent::__construct();

        $this->country = $options['country'];
    }


    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::INVALID] = $translator->translate("Codice Avviamento Postale non corretto");

        $value = strtoupper($value);
        $this->setValue($value);

        if(!is_null($this->country)) {  // if country is non null and is not Italy, then the zip code can be generic
            if($this->country!=='it') {
                return true;
            }
        }

        if (!preg_match("/^([0-9]{5})$/i", $value)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
