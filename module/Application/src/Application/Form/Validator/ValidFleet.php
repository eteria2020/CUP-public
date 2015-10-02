<?php

namespace Application\Form\Validator;

use SharengoCore\Exception\FleetNotFoundException;

use Zend\Validator\AbstractValidator;

class ValidFleet extends AbstractValidator
{
    const UNVALID_FLEET = 'unvalidFleet';

    /**
     * @var FleetService
     */
    private $fleetService;

    protected $messageTemplates = [
        self::UNVALID_FLEET => "Selezionare la città preferita"
    ];

    public function __construct($options)
    {
        parent::__construct();
        $this->fleetService = $options['fleetService'];
    }

    public function isValid($value)
    {
        $this->setValue($value);

        try {
            $this->fleetService->getFleetById($value);
        } catch (FleetNotFoundException $e) {
            $this->error(self::UNVALID_FLEET);
            return false;
        }

        return true;
    }
}
