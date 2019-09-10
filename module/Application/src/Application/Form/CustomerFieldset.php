<?php

namespace Application\Form;


use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\FleetService;
use SharengoCore\Service\ProvincesService;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Fieldset;
use Zend\Session\Container;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class CustomerFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var ProvincesService
     */
    private $provincesService;

    /**
     * @var FleetService
     */
    private $fleetService;

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CountriesService $mondoService,
        CustomersService $customersService,
        AuthenticationService $userService,
        ProvincesService $provincesService,
        FleetService $fleetService
    ) {
        $this->customersService = $customersService;
        $this->userService = $userService;
        $this->provincesService = $provincesService;
        $this->fleetService = $fleetService;

        parent::__construct('customer', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
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
                'minlength' => 2,
                'maxlength' => 60,
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
                'minlength' => 2,
                'maxlength' => 60,
                'placeholder' => $translator->translate('Cognome'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => [
                'id' => 'email',
                'maxlength' => 64,
                'placeholder' => 'name@name.ext'

            ],
            'options' => [
                'label' => $translator->translate('Email')
            ]
        ]);

        $this->add([
            'name' => 'email2',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => [
                'id' => 'email',
                'maxlength' => 64,
                'placeholder' => 'name@name.ext'

            ],
            'options' => [
                'label' => $translator->translate('Ripeti Email')
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
                'value_options' => $mondoService->getAllCountries()
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
            'name' => 'country',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'country',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Stato di residenza'),
                'value_options' => $mondoService->getAllCountries()
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
                'value_options' => $provincesService->getAllProvinces(),
                'use_hidden_element' => true
            ]
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'address',
                'maxlength' => 60,
                'placeholder' => $translator->translate('Via'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Via'),
            ]
        ]);

        $this->add([
            'name' => 'addressNumber',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'addressNumber',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Numero civico'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Numero civico'),
            ]
        ]);

        $this->add([
            'name' => 'addressInfo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'addressInfo',
                'maxlength' => 60,
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
                'maxlength' => 5,
                'placeholder' => $translator->translate('12345'),
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
                'maxlength' => 60,
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
                    "en" => $translator->translate("inglese"),
                    "sk" => $translator->translate("slovacco"),
                    "nl" => $translator->translate("olandese")
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
            'name' => 'fleet',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'fleet'
            ],
            'options' => [
                'value_options' => $fleetService->getFleetsSelectorArrayNoDummy(
                    [0 => '---']
                )
            ]
        ]);

        $this->add([
            'name' => 'cem',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => [
                'id' => 'cem',
                'maxlength' => 64,
                'placeholder' => 'name@name.ext'

            ],
            'options' => [
                'label' => $translator->translate('PEC')
            ]
        ]);

        $this->add([
            'name' => 'recipientCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'recipientCode',
                'maxlength' => 7,
                'placeholder' => $translator->translate('ABCDEFG'),
            ],
            'options' => [
                'label' => $translator->translate('Cod. destinatario'),
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
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
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 60
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
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 60
                        ]
                    ]
                ],
            ],
            'email' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringToLower'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateEmail',
                        'options' => [
                            'customerService' => $this->customersService,
                            'avoid' => [
                                $this->userService->getIdentity()->getEmail()
                            ]
                        ]
                    ],
                    [
                        'name' => 'SharengoCore\Form\Validator\DisposableEmail'
                    ]
                ]
            ],
            'email2' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringToLower'
                    ]
                ],
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
                    ],
                    [
                        'name' => 'SharengoCore\Form\Validator\DisposableEmail'
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
                        'name' => 'Application\Form\Validator\EighteenDate',
                    ],
                    [
                        'name' => 'Application\Form\Validator\NotTooOld'
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
            'addressNumber' => [
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
                ]
            ],
            'language' => [
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
                            'customerService' => $this->customersService,
                            'avoid' => [
                                $this->userService->getIdentity()->getTaxCode()
                            ]
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\CoherentTaxCode',
                        'options' => [
                            'customerService' => $this->customersService
                        ],
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
            'fleet' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\ValidFleet',
                        'options' => [
                            'fleetService' => $this->fleetService
                        ]
                    ]
                ]
            ],
            'cem' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringToLower'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                ]
            ],
            'recipientCode' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\RecipientCode'
                    ]
                ]
            ],
        ];
    }
}
