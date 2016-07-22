<?php

namespace Application\Form;

use Zend\Form\Form;

class PromoCodeForm extends Form
{
    public function __construct(PromoCodeFieldset $promoCodeFieldset) {

        parent::__construct('promo-form');
        $this->setAttribute('method', 'post');

        $this->add($promoCodeFieldset);
    }

    /**
     * Returns true if code represents a standard PromoCode, false otherwise
     * i.e. Carrefour codes
     *
     * @param string $code
     * @return boolean
     */
    public function isStandardPromoCode($code)
    {
        return strlen($code) <= 6;
    }
}
