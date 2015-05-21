<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class BLicense extends AbstractValidator
{
    const BLICENSE = 'BLicenseFrom1Year';

    private $customerService;

    protected $messageTemplates = [
        self::BLICENSE => "E' necessario essere in possesso della patente B"
    ];

    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        // check if the customer has the B license
        if (!in_array('B', $context['driverLicenseCategories'])) {
            $this->error(self::BLICENSE);
            return false;
        }

        return true;
    }
}