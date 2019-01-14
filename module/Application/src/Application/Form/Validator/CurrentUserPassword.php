<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class CurrentUserPassword extends AbstractValidator
{
    const WRONG = 'wrongPassword';

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $userService;

    protected $messageTemplates = [
        self::WRONG => "La password inserita non è quella corretta"
    ];

    public function __construct($options)
    {
        parent::__construct();
        $translator = new \Zend\I18n\Translator\Translator();
        $messageTemplates[ self::WRONG] = $translator->translate("La password inserita non è quella corretta");

        $this->userService = $options['userService'];
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $user = $this->userService->getIdentity();

        if ($user->getPassword() != hash("MD5", $value)) {
            $this->error(self::WRONG);
            return false;
        }

        return true;
    }
}