<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;
use Application\Form\UserFieldset;
use Application\Form\PromoCodeFieldset;
use Zend\Session\Container;
use SharengoCore\Entity\Customers;

class RegistrationForm extends Form
{
    const SESSION_KEY = 'formValidation';

    const FORM_DATA = 'userData';

    const PROMO_CODE = 'promoCode';

    private $container;

    private $promoCodeContainer;

    public function __construct(
        Translator $translator,
        UserFieldset $userFieldset,
        PromoCodeFieldset $promoCodeFieldset
    ) {
        parent::__construct('registration-form');
        $this->setAttribute('class', 'form-signup');
        $this->setAttribute('method', 'post');

        $this->add($userFieldset);

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

    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        if (isset($data['user'])) {
            $this->excludeTaxCodeValidationForForeigners(
                $data['user']['taxCode'],
                $data['user']['birthCountry']
            );
        }

        return parent::setData($data);
    }

    /**
     * @param string $birthCountry
     */
    private function excludeTaxCodeValidationForForeigners($taxCode, $birthCountry)
    {
        $userValidationGroup = array_keys($this->getBaseFieldset()->getElements());

        if (empty($taxCode) && $birthCountry !== 'it') {
            $userValidationGroup = array_diff($userValidationGroup, ['taxCode']);
        }

        $this->setValidationGroup([
            'user' => $userValidationGroup,
            'promocode'
        ]);
    }
}
