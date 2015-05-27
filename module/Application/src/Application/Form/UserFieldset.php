<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ProvincesService;

use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class UserFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $customersService;

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CountriesService $countriesService,
        CustomersService $customersService,
        ProvincesService $provincesService
    ) {
        $this->customersService = $customersService;

        parent::__construct('user', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        $this->add([
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => [
                'id' => 'email',
                'placeholder' => 'Digita la tua email',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Email')
            ]
        ]);

        $this->add([
            'name' => 'email2',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => [
                'id' => 'email2',
                'placeholder' => 'Inserisci di nuovo la email',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Email')
            ]
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'id' => 'password',
                'placeholder' => 'Imposta la tua password',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Password')
            ]
        ]);

        $this->add([
            'name' => 'password2',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'id' => 'password2',
                'placeholder' => 'Inserisci di nuovo la password',
                'class' => 'required'

            ]
        ]);

        $this->add([
            'name' => 'gender',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'gender'
            ],
            'options' => [
                'label' => $translator->translate('Titolo'),
                'value_options' => [
                    'male' => $translator->translate('Sig.'),
                    'female' => $translator->translate('Sig.ra')
                ]
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
            'name' => 'birthDate',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'birthDate',
                'class' => 'required datepicker-date',
                'max' => date_create()->format('d-m-Y'),
                'placeholder' => $translator->translate('dd-mm-aaaa'),
                'type' => 'text'
            ],
            'options' => [
                'label' => $translator->translate('Data di nascita'),
                'format' => 'd-m-Y'
            ]
        ]);

        $this->add([
            'name' => 'birthCountry',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'birthCountry',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Stato di nascita'),
                'value_options' => $countriesService->getAllCountries()
            ]
        ]);

        $this->add([
            'name' => 'birthProvince',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'birthProvince',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Provincia di nascita (EE = estero)'),
                'value_options' => $provincesService->getAllProvinces()
            ]
        ]);

        $this->add([
            'name' => 'birthTown',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'birthTown',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Luogo di nascita'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Comune di nascita'),
            ]
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'address',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Via e numero civico'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Via e numero civico'),
            ]
        ]);

        $this->add([
            'name' => 'addressInfo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'addressInfo',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Informazioni aggiuntive'),
            ],
            'options' => [
                'label' => $translator->translate('Informazioni aggiuntive'),
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
                'maxlength' => 16,
                'placeholder' => $translator->translate('Città'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Città'),
            ]
        ]);

        $this->add([
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
        ]);

        $this->add([
            'name' => 'taxCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'taxCode',
                'maxlength' => 16,
                'placeholder' => 'XXXXXXXXXXXXXXXX',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Codice fiscale'),
            ]
        ]);

        $this->add([
            'name' => 'vat',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'vat',
                'maxlength' => 13,
                'placeholder' => 'ITNNNNNNNNNNN'
            ],
            'options' => [
                'label' => $translator->translate('Partita IVA (opzionale)'),
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
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'phone',
                'maxlength' => 13,
                'placeholder' => $translator->translate('Telefono'),
            ],
            'options' => [
                'label' => $translator->translate('Telefono'),
            ]
        ]);

        $this->add([
            'name' => 'privacy',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'privacy'
            ],
            'options' => [
                'label' => $translator->translate('Privacy'),
                'value_options' => [
                    '0' => $translator->translate('Accetto'),
                    '1' => $translator->translate('Non accetto')
                ]
            ]
        ]);

        /*$this->add([
            'name' => 'generalConditions',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'generalConditions'
            ],
            'options' => [
                'label' => $translator->translate('Condizioni generali di contratto'),
                'value_options' => [
                    '0' => $translator->translate('Accetto'),
                    '1' => $translator->translate('Non accetto')
                ]
            ]
        ]);*/

        $this->add([
            'name' => 'specialConditions',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'specialConditions'
            ],
            'options' => [
                'label' => $translator->translate('Condizioni speciali di contratto'),
                'value_options' => [
                    '0' => $translator->translate('Accetto'),
                    '1' => $translator->translate('Non accetto')
                ]
            ]
        ]);

        $this->add([
            'name' => 'profilingCounter',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'profilingCounter'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateEmail',
                        'options' => [
                            'customerService' => $this->customersService
                        ]
                    ]
                ]
            ],
            'email2' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'email'
                        ]
                    ]
                ]
            ],
            'password' => [
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
                            'min' => 8
                        ]
                    ]
                ]
            ],
            'password2' => [
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
                            'min' => 8
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password'
                        ]
                    ]
                ]
            ],
            'name' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'surname' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'birthDate' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd-m-Y'
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\EighteenDate'
                    ]
                ]
            ],
            'birthTown' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'birthCountry' => [
                'required' => true
            ],
            'birthProvince' => [
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
                        'name' => 'Application\Form\Validator\DuplicateTaxCode',
                        'options' => [
                            'customerService' => $this->customersService
                        ]
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
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 11,
                            'max' => 13
                        ]
                    ]
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
                    ]
                ]
            ],
            'phone' => [
                'required' => false,
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
                    ]
                ]
            ],
            'privacy' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\OptionAccepted'
                    ]
                ]
            ],
            /*'generalConditions' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\OptionAccepted'
                    ]
                ]
            ],*/
            'specialConditions' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\OptionAccepted'
                    ]
                ]
            ],
        ];
    }
}
