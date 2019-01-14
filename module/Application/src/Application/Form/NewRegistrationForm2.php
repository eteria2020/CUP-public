<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Validator\Identical;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;
use SharengoCore\Entity\Customers;
//use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\File\MimeType;
//use Zend\Stdlib\Hydrator\HydratorInterface;

class NewRegistrationForm2 extends Form
{
    const SESSION_KEY = 'formValidation';

    const FORM_DATA = 'user1';

    const PROMO_CODE = 'promoCode';

    /**
     * @var Translator $translator
     */
    private $translator;

    /**
     * @var $container
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $entityManager;


    public function __construct(
        Translator $translator,
        PromoCodeFieldset $promoCodeFieldset,
        NewUserFieldset2 $newUserFieldset2,
        EntityManager $entityManager
    ) {
        parent::__construct('registration-form-2');

        $this->translator = $translator;
        $this->entityManager = $entityManager;


        $this->setAttribute('class', 'form-signup');
        $this->setAttribute('method', 'post');

        $this->add($newUserFieldset2);
        $this->add($promoCodeFieldset);

        $this->addElements();
        //$this->addInputFilter();

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }
    private function addElements()
    {
        $this->add([
            'name' => 'signature',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => [
                'id' => 'signature'
            ],
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => 'true',
                'unchecked_value' => 'false'
            ]
        ]);

        $this->add([
            'name' => 'drivers-license-front',
            'type' => 'Zend\Form\Element\File',
            'attributes' => [
                'id' => 'drivers-license-front',
                'multiple' => false
            ]
        ]);

        $this->add([
            'name' => 'drivers-license-back',
            'type' => 'Zend\Form\Element\File',
            'attributes' => [
                'id' => 'drivers-license-back',
                'multiple' => false
            ]
        ]);

        $this->add([
            'name' => 'identity-front',
            'type' => 'Zend\Form\Element\File',
            'attributes' => [
                'id' => 'identity-front',
                'multiple' => false
            ]
        ]);

        $this->add([
            'name' => 'identity-back',
            'type' => 'Zend\Form\Element\File',
            'attributes' => [
                'id' => 'identity-back',
                'multiple' => false
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

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $inputFilter->remove('signature');
        $inputFilter->remove('drivers-license-front');
        $inputFilter->remove('drivers-license-back');
        $inputFilter->remove('identity-front');
        $inputFilter->remove('identity-back');

        $inputFactory = new InputFactory();

        $fieldValidator = [
            [
                'name' => 'Callback',
                'options' => [
                    'callback' => function($value, $context = array()) {
                        return true;
                    }
                ]
            ]
        ];

        if($this->get('user1')->get('driverLicenseForeign')->getValue() == 'true'){
            $fieldValidator = [
                [
                    'name' => 'File/MimeType',
                    'options' => [
                        'mimeType' => 'image,application/pdf',
                        'messages' => [
                            MimeType::FALSE_TYPE => $this->translator->translate('Il file caricato ha un formato non valido; sono accettati solo formati di immagini e pdf'),
                            MimeType::NOT_DETECTED => $this->translator->translate('Non è stato possibile verificare il formato del file'),
                            MimeType::NOT_READABLE => $this->translator->translate('Il file caricato non è leggibile o non esiste')
                        ]
                    ]
                ]
            ];
        }

        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'signature',
                'required' => $this->get('user1')->get('driverLicenseForeign')->getValue() == 'true',
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => $this->get('user1')->get('driverLicenseForeign')->getValue(),
                            'messages' => [
                                Identical::NOT_SAME => $this->translator->translate("E\' necessario confermare e sottoscrivere la dichiarazione"),
                            ],
                        ],
                    ],
                ]
            ])
        );
        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'drivers-license-front',
                'required' => $this->get('user1')->get('driverLicenseForeign')->getValue() == 'true',
                'validators' => $fieldValidator
            ])
        );
        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'drivers-license-back',
                'required' => $this->get('user1')->get('driverLicenseForeign')->getValue() == 'true',
                'validators' => $fieldValidator
            ])
        );

        $inputFilter->add(
            $inputFactory->createInput([
            'name' => 'identity-front',
            'required' => $this->get('user1')->get('driverLicenseForeign')->getValue() == 'true',
            'validators' => $fieldValidator
            ])
        );

        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'identity-back',
                'required' => $this->get('user1')->get('driverLicenseForeign')->getValue() == 'true',
                'validators' => $fieldValidator
            ])
        );
        //$this->setInputFilter($inputFilter);
        return $inputFilter;
    }

}
