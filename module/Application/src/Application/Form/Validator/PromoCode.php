<?php

namespace Application\Form\Validator;

use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\PromoCodesService;

use Zend\Validator\AbstractValidator;

class PromoCode extends AbstractValidator
{
    /**
     * @var string
     */
    const WRONG = 'promoCode';

    /**
     * @var PromoCodesService
     */
    private $promoCodesService;

    /**
     * @var CarrefourService|null
     */
    private $carrefourService;

    /**
     * @var string[]
     */
    protected $messageTemplates = [
        self::WRONG => "Il codice inserito non è valido"
    ];

    /**
     * @param array $options
     */
    public function __construct($options)
    {
        parent::__construct();
        $this->promoCodesService = $options['promoCodesService'];
        $this->carrefourService = $options['carrefourService'];
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $isStandardValid = $this->promoCodesService->isValid($value);
        $isCarrefourValid = false;
        if ($this->carrefourService instanceof CarrefourService) {
            $isCarrefourValid = $this->carrefourService->isValid($value);
        }

        if ($isStandardValid || $isCarrefourValid) {
            return true;
        }

        $this->error(self::WRONG);
        return false;
    }
}
