<?php

namespace Application\Form\Validator;

use SharengoCore\Exception\CodeAlreadyUsedException;
use SharengoCore\Exception\NotAValidCodeException;
use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesOnceService;

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
     * @var PromoCodesOnceService
     */
    private $promoCodesOnceService;

    /**
     * @var CarrefourService|null
     */
    private $carrefourService;

    /**
     * @var string[]
     */
    protected $messageTemplates = [
        self::WRONG_CODE => "Il codice promo inserito non è valido",
        self::USED_CODE => "Il codice promo è già stato utilizzato"
    ];

    /**
     * @param array $options
     */
    public function __construct($options)
    {
        parent::__construct();
        $this->promoCodesService = $options['promoCodesService'];
        $this->promoCodesOnceService = $options['promoCodesOnceService'];
        $this->carrefourService = $options['carrefourService'];
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result =FALSE;

        $this->setValue($value);

        if($this->promoCodesService->isValid($value)){
             $result =TRUE;
        }else {
            if($this->promoCodesOnceService->isValid($value)){
                $result =TRUE;
            } else {
                if ($this->carrefourService instanceof CarrefourService) {
                    try {
                        $this->carrefourService->checkCarrefourCode($value);
                        $result =TRUE;
                    } catch (CodeAlreadyUsedException $e) {
                        $this->error(self::USED_CODE);
                    } catch (NotAValidCodeException $e) {
                        $this->error(self::WRONG_CODE);
                    }
                }else {
                    $this->error(self::WRONG_CODE);
                }
            }
        }

        return $result;

//        if (!$isStandardValid && $this->carrefourService instanceof CarrefourService) {
//            try {
//                $this->carrefourService->checkCarrefourCode($value);
//            } catch (CodeAlreadyUsedException $e) {
//                $this->error(self::USED_CODE);
//                return false;
//            } catch (NotAValidCodeException $e) {
//                $this->error(self::WRONG_CODE);
//                return false;
//            }
//        } elseif (!$isStandardValid) {
//            $this->error(self::WRONG_CODE);
//            return false;
//        }
//
//        return true;

    }
}
