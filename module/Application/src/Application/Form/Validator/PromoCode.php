<?php

namespace Application\Form\Validator;

use SharengoCore\Exception\CodeAlreadyUsedException;
use SharengoCore\Exception\NotAValidCodeException;
use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\PromoCodesService;

use Zend\Validator\AbstractValidator;

class PromoCode extends AbstractValidator
{
    /**
     * @var string
     */
    const WRONG_CODE = 'code';

    /**
     * @var string
     */
    const USED_CODE = 'used';

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
        self::WRONG_CODE => "Il codice inserito non è valido",
        self::USED_CODE => "Il codice è già stato utilizzato"
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

        if (!$isStandardValid && $this->carrefourService instanceof CarrefourService) {
            try {
                $this->carrefourService->checkCarrefourCode($value);
            } catch (CodeAlreadyUsedException $e) {
                $this->error(self::USED_CODE);
                return false;
            } catch (NotAValidCodeException $e) {
                $this->error(self::WRONG_CODE);
                return false;
            }
        } elseif (!$isStandardValid) {
            $this->error(self::WRONG_CODE);
            return false;
        }

        return true;
    }
}
