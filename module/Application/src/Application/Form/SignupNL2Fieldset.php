<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ProvincesService;
use SharengoCore\Service\CountriesService;

use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class SignupNL2Fieldset extends Fieldset implements InputFilterProviderInterface {

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
     * @var ProvincesService
     */
    private $serverInstance;

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CustomersService $customersService,
        CountriesService $countriesService,
        ProvincesService $provincesService,
        $serverInstance
        ) {

        $this->translator = $translator;
        $this->customersService = $customersService;
        $this->countriesService = $countriesService;
        $this->provincesService = $provincesService;
        $this->serverInstance = $serverInstance;

        parent::__construct('user1', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        $this->add([
            'name' => 'driverLicenseForeign',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => [
                'id' => 'driverLicenseForeign'
            ],
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => 'true',
                'unchecked_value' => 'true'
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
                'maxlength' => 32,
                'placeholder' => $this->translator->translate('Cognome'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'town',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'town',
                'maxlength' => 64,
                'placeholder' => $this->translator->translate('Città'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Città'),
            ]
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'address',
                'maxlength' => 64,
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
                'maxlength' => 12,
                'placeholder' => $this->translator->translate('CAP'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('CAP'),
            ]
        ]);

        $this->add([
            'name' => 'birthDate',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'birthDate',
                'class' => 'required datepicker-date',
                'max' => date_create()->format('Y-m-d'),
                'type' => 'text'
            ],
            'options' => [
                'label' => $translator->translate('Data di nascita')
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
                'value_options' => $this->countriesService->getAllCountries( "Nederland")
            ]
        ]);

        $this->add([
            'name' => 'birthProvince',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'birthProvince',
                'placeholder' => $translator->translate('EE = estero'),
                'class' => 'required',
                'maxlength' => 2
            ],
            'options' => [
                'label' => $translator->translate('Provincia di nascita (EE = estero)'),
                'value_options' => $provincesService->getAllProvinces(),
                'use_hidden_element' => true
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
            'name' => 'taxCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'taxCode',
                'maxlength' => 9,
                'placeholder' => $this->translator->translate('123456789'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Numero Identificativo'),
            ]
        ]);

        $this->add([
            'name' => 'dialCode',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'dialCode'
            ],
            'options' => [
                'label' => $translator->translate('Prefisso internazionale'),
                'value_options' => $countriesService->getAllPhoneCodeByCountry('Nederland')
            ]
        ]);

        $this->add([
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'mobile',
                'maxlength' => 13,
                'placeholder' => $this->translator->translate('Cellulare'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $this->translator->translate('Cellulare'),
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

    }

    public function getInputFilterSpecification() {

        return [
            'name' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
                        ]
                    ]
                ]
            ],
            'surname' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 32
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
            'birthDate' => [
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
                        'name' => 'Application\Form\Validator\EighteenDate'
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
                        'name' => 'Application\Form\Validator\IdNumber',
                        'options' => [
                            'length' => 9,
                        ],
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
            'dialCode' => [
                'required' => true
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
                'required' => false,
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
        ];
    }
}