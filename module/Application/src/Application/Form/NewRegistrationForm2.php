<?php

namespace Application\Form;

use Doctrine\Entity;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;
use Application\Form\UserFieldset;
use Zend\Session\Container;
use SharengoCore\Entity\Customers;

class NewRegistrationForm2 extends Form
{
    const SESSION_KEY = 'formValidation';

    const FORM_DATA = 'user1';

    const PROMO_CODE = 'promoCode';

    private $container;

    private $entityManager;

    public function __construct(
        Translator $translator,
        PromoCodeFieldset $promoCodeFieldset,
        NewUserFieldset2 $newUserFieldset2,
        EntityManager $entityManager
    ) {
        parent::__construct('registration-form');

        $this->entityManager = $entityManager;
        $this->setAttribute('class', 'form-signup');
        $this->setAttribute('method', 'post');

        $this->add($newUserFieldset2);
        $this->add($promoCodeFieldset);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }

    private function getContainer()
    {
        if (isset($this->container)) {
            return $this->container;
        }

        return new Container(self::SESSION_KEY);
    }

    private function getPromoCodeContainer()
    {
        if (isset($this->promoCodeContainer)) {
            return $this->promoCodeContainer;
        }

        return new Container(self::SESSION_KEY . 'PromoCode');
    }

    public function registerCustomerData(Customers $customer)
    {
        $container = $this->getContainer();
        $container->offsetSet(self::FORM_DATA, $customer);
    }

    public function registerPromoCodeData($promoCode)
    {
        $promoCodeContainer = $this->getPromoCodeContainer();
        $promoCodeContainer->offsetSet(self::PROMO_CODE, $promoCode);
    }

    public function registerData($promoCode)
    {
        $container = $this->getContainer();
        $container->offsetSet(self::FORM_DATA, $this->getData());
        $this->registerPromoCodeData($promoCode);
    }

    public function getRegisteredData()
    {
        $container = $this->getContainer();
        return $container->offsetGet(self::FORM_DATA);
    }

    public function getRegisteredDataPromoCode()
    {
        $promoCodeContainer = $this->getPromoCodeContainer();
        return $promoCodeContainer->offsetGet(self::PROMO_CODE);
    }

    public function clearRegisteredData()
    {
        $container = $this->getContainer();
        $container->offsetUnset(self::FORM_DATA);
        $promoCodeContainer = $this->getPromoCodeContainer();
        $promoCodeContainer->offsetUnset(self::PROMO_CODE);
    }

}
