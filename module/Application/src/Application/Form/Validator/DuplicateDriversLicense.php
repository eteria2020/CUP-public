<?php

namespace Application\Form\Validator;

use Zend\Validator\AbstractValidator;

class DuplicateDriversLicense extends AbstractValidator
{
    const DUPLICATE = 'duplicateDriversLicense';

    private $customerService;

    /**
     * @var array
     */
    private $driverLicenseToAvoid = [];

    protected $messageTemplates = [
        self::DUPLICATE => "Esiste già un utente con la stessa patente"
    ];

    public function __construct($options)
    {
        parent::__construct();
        $this->customerService = $options['customerService'];
        if (isset($options['avoid'])) {
            $this->driverLicenseToAvoid = $options['avoid'];
        }
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $customer = $this->customerService->findByDriversLicense($value);

        if (!empty($customer) && !in_array($customer[0]->getDriverLicense(), $this->driverLicenseToAvoid)) {
            $this->error(self::DUPLICATE);
            return false;
        }

        return true;
    }
}
