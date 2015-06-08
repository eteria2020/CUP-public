<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;
use Application\Form\UserFieldset;
use Zend\Session\Container;
use SharengoCore\Entity\Customers;

class RegistrationForm extends Form
{
    const SESSION_KEY = 'formValidation';

    const FORM_DATA = 'userData';

    private $container;

    public function __construct(
        Translator $translator,
        UserFieldset $userFieldset
    ) {
        parent::__construct('registration-form');
        $this->setAttribute('class', 'form-signup');
        $this->setAttribute('method', 'post');

        $this->add($userFieldset);

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

    public function registerCustomerData(Customers $customer)
    {
        $container = $this->getContainer();
        $container->offsetSet(self::FORM_DATA, $customer);
    }

    public function registerData()
    {
        $container = $this->getContainer();
        $container->offsetSet(self::FORM_DATA, $this->getData());
    }

    public function getRegisteredData()
    {
        $container = $this->getContainer();
        return $container->offsetGet(self::FORM_DATA);
    }

    public function clearRegisteredData()
    {
        $container = $this->getContainer();
        $container->offsetUnset(self::FORM_DATA);
    }
}
