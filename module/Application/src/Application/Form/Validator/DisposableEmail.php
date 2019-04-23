<?php


namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;
use EmailChecker\EmailChecker;

class DisposableEmail extends AbstractValidator
{
    const DISPOSABLE = 'Disposable';

    protected $messageTemplates = [
        self::DISPOSABLE => "Indirizzo email non accettato"
    ];

    public function __construct()
    {
        parent::__construct();
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::DISPOSABLE] = $translator->translate("Indirizzo email non accettato");
    }


    public function isValid($value) {

        $checker = new EmailChecker();
        if(!$checker->isValid($value)) {
            $this->error(self::DISPOSABLE);
            return false;
        } else {
            return true;
        }
    }
}