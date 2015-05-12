<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class DuplicateDriversLicense extends AbstractValidator
{
    const DUPLICATE = 'duplicateDriversLicense';

    private $customerService;

    protected $messageTemplates = [
        self::DUPLICATE => "Esiste già un utente con la stessa patente"
    ];

    public function __construct($options)
    {
        parent::__construct();
        $this->customerService = $options['customerService'];
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $customer = $this->customerService->findByDriversLicense(strtoupper($value));

        if (!empty($customer)) {
            $this->error(self::DUPLICATE);
            return false;
        }

        return true;
    }
}
