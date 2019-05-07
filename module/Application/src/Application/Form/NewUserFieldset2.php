<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;

use SharengoCore\Service\CustomersService;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\ProvincesService;

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
     * @var Translator $translator
     */
    private $translator;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var CountriesService
     */
    private $countriesService;

    /**
     * @var ProvincesService
     */
    private $provincesService;

    /**
     * NewUserFieldset2 constructor.
     * @param Translator $translator
     * @param HydratorInterface $hydrator
     * @param CustomersService $customersService
     * @param CountriesService $countriesService
     * @param ProvincesService $provincesService
     */
    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CustomersService $customersService,
        CountriesService $countriesService,
        ProvincesService $provincesService) {

        $this->translator = $translator;
        $this->customersService = $customersService;
        $this->countriesService = $countriesService;
        $this->provincesService = $provincesService;

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
                'minlength' => 2,
                'maxlength' => 60,
                'placeholder' => $this->translator->translate('Nome sulla patente'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Nome')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseSurname',
            'continue_if_empty' => true,
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicenseSurname',
                'minlength' => 2,
                'maxlength' => 60,
                'placeholder' => $this->translator->translate('Cognome sulla patente'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'minlength' => 2,
                'maxlength' => 60,
                'placeholder' => $this->translator->translate('Nome'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Nome')
            ]
        ]);

        $this->add([
            'name' => 'surname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'surname',
                'minlength' => 2,
                'maxlength' => 60,
                'placeholder' => $this->translator->translate('Cognome'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'country',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'country',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Stato di residenza'),
                'value_options' => $this->countriesService->getAllCountries()
            ]
        ]);

        $this->add([
            'name' => 'province',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'province',
                'placeholder' => $translator->translate('EE = estero'),
                'class' => 'required',
                'maxlength' => 2
            ],
            'options' => [
                'label' => $translator->translate('Provincia di residenza (EE = estero)'),
                'value_options' => $this->provincesService->getAllProvinces(),
                'use_hidden_element' => true
            ]
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'address',
                'maxlength' => 60,
                'placeholder' => $this->translator->translate('Indirizzo'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Indirizzo'),
            ]
        ]);

        $this->add([
            'name' => 'civico',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'civico',
                'maxlength' => 14,
                'placeholder' => $this->translator->translate('Numero civico'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Numero civico'),
            ]
        ]);


        $this->add([
            'name' => 'zipCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'zipCode',
                'maxlength' => 5,
                'placeholder' => $this->translator->translate('12345'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('CAP'),
            ]
        ]);

        $this->add([
            'name' => 'town',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'town',
                'maxlength' => 60,
                'placeholder' => $this->translator->translate('Città'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Città'),
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
                'placeholder' => $this->translator->translate('ABCDEF12G34H567I'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Codice fiscale'),
            ]
        ]);

        $this->add([
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'mobile',
                'maxlength' => 13,
                'placeholder' => $this->translator->translate('Cellulare'),
            ],
            'options' => [
                'label' => $this->translator->translate('Cellulare'),
            ]
        ]);

        $this->add([
            'name' => 'smsCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'smsCode',
                'maxlength' => 4,
                'placeholder' => $this->translator->translate('xxxx'),
            ],
            'options' => [
                'label' => $this->translator->translate('Codice Sms'),
            ]
        ]);

        $this->add([
            'name' => 'driverLicense',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'driverLicense',
                'class' => 'required',
                'placeholder' => $this->translator->translate('XX123412341223'),
            ],
            'options' => [
                'label' => $this->translator->translate('Patente')
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseExpire',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'driverLicenseExpire',
                'class' => 'required datepicker-date',
                'min' => date_create()->format('d-m-Y'),
                'placeholder' => $this->translator->translate('dd-mm-aaaa'),
                'type' => 'text'
            ],
            'options' => [
                'label' => $this->translator->translate('Data di scadenza'),
                'format' => 'd-m-Y'
            ]
        ]);

        $this->add([
            'name' => 'driverLicenseCountry',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'driverLicenseCountry',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Rilasciata da (nazione)'),
                'value_options' => $this->countriesService->getAllCountries(),
            ]
        ]);

        $this->add([
            'name' => 'vat',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'vat',
                'maxlength' => 13,
                'placeholder' => $this->translator->translate('Partita IVA')
            ],
            'options' => [
                'label' => $translator->translate('Partita IVA'),
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
                            'max' => 60
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
                            'max' => 60
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
                            'messages' => [NotEmpty::IS_EMPTY => $this->translator->translate('Il nome sulla patente non coincide col codice fiscale. Verifica di averlo inserito correttamente, se effettivamente diverso inserisci qui quello corretto')]
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 60
                        ]
                    ],
                    [
                        'name' => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => $this->translator->translate('Il nome non coincide con quello presente sul codice fiscale')
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
                            'messages' => [NotEmpty::IS_EMPTY => $this->translator->translate('Il cognome sulla patente non coincide con quello del codice fiscale, inserisci qui quello corretto')]
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 60
                        ]
                    ],
                    [
                        'name' => 'Callback',
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => $this->translator->translate('Il cognome non coincide con quello presente sul codice fiscale')
                            ],
                            'callback' => function($value, $context = array()) {
                                return ($this->isSurnameRequired() && $this->surnameCode($this->get('surname')->getValue()) == strtoupper(substr($this->get('taxCode')->getValue(), 0, 3)));
                            }
                        ]
                    ]
                ]
            ],
            'country' => [
                'required' => true
            ],
            'province' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
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
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'message' =>  $this->translator->translate('Il Codice Avviamento Postale (CAP) non può essere vuoto')
                        ],
                    ],
                    [
                        'name' => 'Application\Form\Validator\ZipCode',
                        'options' => [
                            'country' =>  new Container('country')
                        ]
                    ]
                ]
            ],
            'town' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'message' =>  $this->translator->translate('Il dato è richiesto e non può essere vuoto')
                        ],
                    ],
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
                                Callback::INVALID_VALUE => $this->translator->translate('Il numero di telefono inserito non corrisponde a quello del codice di verifica')
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
                                Callback::INVALID_VALUE => $this->translator->translate('Il codice inserito non corrisponde a quello inviato')
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
            'driverLicenseCountry' => [
                'required' => true,
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