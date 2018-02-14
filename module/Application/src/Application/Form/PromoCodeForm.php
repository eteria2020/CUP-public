<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Captcha;

class PromoCodeForm extends Form {

    public function __construct(PromoCodeFieldset $promoCodeFieldset) {

        parent::__construct('promo-form');
        $this->setAttribute('method', 'post');

        $captcha = new \Zend\Form\Element\Captcha('captcha');
        $captcha
                ->setCaptcha(new Captcha\Image(array(
                    'name' => 'foo',
                    'wordLen' => 6,
                    'timeout' => 300,
                    'dotNoiseLevel' => 40,
                    'lineNoiseLevel' => 3,
                    'font' => './public/fonts/arial.ttf',
                    'imgDir' => './public/cache',
                    'imgUrl' => '/cache/')));

        $captcha->setOptions(array('label' =>'Inserire il codice che vedi nell\'immagine'));

        $this->add($promoCodeFieldset);
        $this->add($captcha);
    }

}
