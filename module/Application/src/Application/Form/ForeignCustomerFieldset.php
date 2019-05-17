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

class ForeignCustomerFieldset extends Fieldset implements InputFilterProviderInterface
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

    /**
     * @var ServerInstance
     */
    private $serverInstance;

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CountriesService $mondoService,
        CustomersService $customersService,
        AuthenticationService $userService,
        ProvincesService $provincesService,
        FleetService $fleetService,
        $serverInstance
    ) {
        $this->customersService = $customersService;
        $this->userService = $userService;
        $this->provincesService = $provincesService;
        $this->fleetService = $fleetService;
        $this->serverInstance = $serverInstance;

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
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'address',
                'maxlength' => 60,
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
                'maxlength' => 10,
                'placeholder' =>  $translator->translate('1234567890'),
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Numero Identificativo'),
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
                            'length' => $this->taxCodeLength(),
                        ],
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
                ],
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
        ];
    }

    /**
     * Return the max length of IdNumber
     *
     * @return int
     */
    private function taxCodeLength(){
        $length = 10;
        if (!is_null($this->serverInstance) && $this->serverInstance == "nl_NL"){
            $length = 9;
        }

        return $length;
    }
}
