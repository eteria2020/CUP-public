<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class RecipientCode extends AbstractValidator
{
    const RECIPEINT_CODE_FOREIGN = 'XXXXXXX';
    const NUMERIC = 'numeric';
    const LENGTH = 'length';

    protected $messageTemplates = [
        self::LENGTH => "Il codice destinatario deve essere una cifra di 7 caratteri numerici",
        self::NUMERIC => "Il codice destinatario, deve avere solo caratteri numerici"
    ];

    public function isValid($value)
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::LENGTH] = $translator->translate("Il codice destinatario deve essere una cifra di 7 caratteri numerici");
        $messageTemplates[ self::NUMERIC] = $translator->translate("Il codice destinatario, deve avere solo caratteri numerici");
        
        $this->setValue($value);

        if(!($value === self::RECIPEINT_CODE_FOREIGN)) {
            if (!(strlen($value) === 7)) {
                $this->error(self::LENGTH);
                return false;
            }

            if (!preg_match("/^[0-9]+$/", $value)) {
                $this->error(self::NUMERIC);
                return false;
            }            
        }
        
        return true;
    }
}
