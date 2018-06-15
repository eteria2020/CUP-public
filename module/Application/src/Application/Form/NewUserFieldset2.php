<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;

use SharengoCore\Service\CustomersService;


use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Session\Container;
use Zend\Validator\NotEmpty;
use Zend\Validator\Callback;
use Zend\ServiceManager\ServiceLocatorInterface;


class NewUserFieldset2 extends Fieldset implements InputFilterProviderInterface {

    /**
     * @var CustomersService
     */
    private $customersService;


    public function __construct(
    Translator $translator, HydratorInterface $hydrator, CustomersService $customersService) {
        $this->customersService = $customersService;

        parent::__construct('user1', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        $this->add([
            'name' => 'driverLicenseName',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicenseName',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Nome sulla patente'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Nome')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseSurname',
            'continue_if_empty' => true,
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicenseSurname',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Cognome sulla patente'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Nome'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Nome')
            ]
        ]);

        $this->add([
            'name' => 'surname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'surname',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Cognome'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'address',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Indirizzo'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Indirizzo'),
            ]
        ]);

        $this->add([
            'name' => 'civico',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'civico',
                'maxlength' => 14,
                'placeholder' => $translator->translate('Numero civico'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Numero civico'),
            ]
        ]);


        $this->add([
            'name' => 'zipCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'zipCode',
                'maxlength' => 12,
                'placeholder' => $translator->translate('CAP'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('CAP'),
            ]
        ]);

        $this->add([
            'name' => 'town',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'town',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Città'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Città'),
            ]
        ]);

        /*$this->add([
            'name' => 'language',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'language'
            ],
            'options' => [
                'label' => $translator->translate('Lingua preferita'),
                'value_options' => [
                    "it" => $translator->translate("Italiano"),
                    "de" => $translator->translate("tedesco"),
                    "fr" => $translator->translate("francese"),
                    "es" => $translator->translate("spagnolo"),
                    "en" => $translator->translate("inglese"),
                    "ch" => $translator->translate("cinese"),
                    "ru" => $translator->translate("russo"),
                    "pt" => $translator->translate("portoghese")
                ]
            ]
        ]);*/

        $this->add([
            'name' => 'taxCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'taxCode',
                'maxlength' => 16,
                'placeholder' => 'ABCDEF12G34H567I',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Codice fiscale'),
            ]
        ]);

        $this->add([
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'mobile',
                'maxlength' => 13,
                'placeholder' => $translator->translate('Cellulare'),
            ],
            'options' => [
                'label' => $translator->translate('Cellulare'),
            ]
        ]);

        $this->add([
            'name' => 'smsCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'smsCode',
                'maxlength' => 4,
                'placeholder' => $translator->translate('xxxx'),
            ],
            'options' => [
                'label' => $translator->translate('Codice Sms'),
            ]
        ]);

        $this->add([
            'name' => 'driverLicense',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicense',
                'class' => 'required',
                'placeholder' => $translator->translate('XX123412341223'),
            ],
            'options' => [
                'label' => $translator->translate('Patente')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseExpire',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'driverLicenseExpire',
                'class' => 'required datepicker-date',
                'min' => date_create()->format('d-m-Y'),
                'placeholder' => $translator->translate('dd-mm-aaaa'),
                'type' => 'text'
            ],
            'options' => [
                'label' => $translator->translate('Data di scadenza'),
                'format' => 'd-m-Y'
            ]
        ]);

        $this->add([
            'name' => 'vat',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'vat',
                'maxlength' => 13,
                'placeholder' => 'Partita IVA'
            ],
            'options' => [
                'label' => $translator->translate('Partita IVA'),
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseForeign',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => [
                'id' => 'driverLicenseForeign'
            ],
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => 'true',
                'unchecked_value' => 'false'
            ]
        ]);

    }

    public function getInputFilterSpecification() {

        return [
            'driverLicenseName' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'driverLicenseSurname' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'name' => [
                'required' => $this->isNameRequired(),
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [NotEmpty::IS_EMPTY => 'Il nome sulla patente non coincide col codice fiscale. Verifica di averlo inserito correttamente, se effettivamente diverso inserisci qui quello corretto']
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ],
                    [
                        'name' => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Il nome non coincide con quello presente sul codice fiscale'
                            ],
                            'callback' => function($value, $context = array()) {
                                return ($this->isNameRequired() && $this->nameCode($this->get('name')->getValue()) == strtoupper(substr($this->get('taxCode')->getValue(), 3, 3)));
                            }
                        ]
                    ]
                ]
            ],
            'surname' => [
                'required' => $this->isSurnameRequired(),
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [NotEmpty::IS_EMPTY => 'Il cognome sulla patente non coincide con quello del codice fiscale, inserisci qui quello corretto']
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ],
                    [
                        'name' => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Il cognome non coincide con quello presente sul codice fiscale'
                            ],
                            'callback' => function($value, $context = array()) {
                                return ($this->isSurnameRequired() && $this->surnameCode($this->get('surname')->getValue()) == strtoupper(substr($this->get('taxCode')->getValue(), 0, 3)));
                            }
                        ]
                    ]
                ]
            ],
            'address' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'civico' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'zipCode' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'town' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'taxCode' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\TaxCodeSignup',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateTaxCode',
                        'options' => [
                            'customerService' => $this->customersService
                        ],
                        'break_chain_on_failure' => true
                    ],
                ]
            ],
            'mobile' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3
                        ]
                    ],
                    [
                        'name' => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Il numero di telefono inserito non corrisponde a quello del codice di verifica'
                            ],
                            'callback' => function($value, $context = array()) {
                                $smsVerification = new Container('smsVerification');
                                $isValid = $value == $smsVerification->offsetGet('mobile');
                                return $isValid;
                                }
                        ]
                    ]
                ]
            ],
            'smsCode' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 4,
                            'max' => 4
                        ]
                    ],
                    [
                        'name' => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Il codice inserito non corrisponde a quello inviato'
                            ],
                            'callback' => function($value, $context = array()) {

                                $smsVerification = new Container('smsVerification');
                                $isValid = $value == $smsVerification->offsetGet('code');
                                return $isValid;
                            }
                        ]
                    ]
                ]
            ],
            'driverLicense' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 6,
                            'max' => 32
                        ]
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateDriversLicense',
                        'options' => [
                            'customerService' => $this->customersService
                        ]
                    ]
                ]
            ],
            'driverLicenseExpire' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\DateFormat'
                    ],
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd-m-Y'
                        ]
                    ],
                    [
                        'name' => 'Application\Form\Validator\DateFromToday'
                    ]
                ]
            ],
            'vat' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\VatNumber'
                    ]
                ]
            ],
        ];
    }

    public function isNameRequired()
    {
        if ($this->get('taxCode')->getValue() == ""){
            return false;
        }

        $name = strtoupper(substr($this->get('taxCode')->getValue(), 3, 3));
        if($name != $this->nameCode($this->get('driverLicenseName')->getValue())){
            return true;
        }
        return false;
    }

    public function isSurnameRequired()
    {
        if ($this->get('taxCode')->getValue() == ""){
            return false;
        }

        $name = strtoupper(substr($this->get('taxCode')->getValue(), 0, 3));

        if($name != $this->surnameCode($this->get('driverLicenseSurname')->getValue())){
            return true;
        }
        return false;
    }

    private function estraiVocali($str){
        return preg_replace('/[^AEIOU]/','', strtoupper($str));
    }

    private function estraiConsonanti($str) {
        return preg_replace('/[^BCDFGHJKLMNPQRSTVWXYZ]/', '', strtoupper($str));
    }

    private function surnameCode($surname) {
        $codeSurname = $this->estraiConsonanti($surname) . $this->estraiVocali($surname) . 'XXX';
        return strtoupper(substr($codeSurname, 0, 3));
    }

    private function nameCode($name) {
        $codNome = $this->estraiConsonanti($name);
        if (strlen($codNome) >= 4) {
            $codNome = $codNome[0] . $codNome[2] . $codNome[3];
        } else {
            $codNome .= $this->estraiVocali($name) . 'XXX';
            $codNome = substr($codNome, 0, 3);
        }
        return strtoupper($codNome);
    }

}