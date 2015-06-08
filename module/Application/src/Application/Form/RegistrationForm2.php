<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Mvc\I18n\Translator;
use Application\Form\UserFieldset;
use Zend\Session\Container;

class RegistrationForm2 extends Form
{
    const SESSION_KEY = 'formValidation';

    const FORM_DATA = 'driverData';

    private $container;

    public function __construct(
        Translator $translator,
        DriverFieldset $driverFieldset
    ) {
        parent::__construct('registration-form-2');
        $this->setAttribute('class', 'form-signup');
        $this->setAttribute('method', 'post');

        $this->add($driverFieldset);

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

    public function registerData()
    {
        $container = new Container(self::SESSION_KEY);
        $container->offsetSet(self::FORM_DATA, $this->getData());
    }

    public function getRegisteredData()
    {
        $container = new Container(self::SESSION_KEY);
        return $container->offsetGet(self::FORM_DATA);
    }

    public function clearRegisteredData()
    {
        $container = $this->getContainer();
        $container->offsetUnset(self::FORM_DATA);
    }
}
