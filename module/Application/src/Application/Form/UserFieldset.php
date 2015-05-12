<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
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
        CountriesService $mondoService,
        CustomersService $customersService
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
                'maxlength' => 64,
                'placeholder' => 'name@name.ext'

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
                'placeholder' => '********'
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
                'placeholder' => '********'

            ]
        ]);

        $this->add([
            'name' => 'pin',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'pin',
                'maxlength' => 4,
                'placeholder' => $translator->translate('Es. 0000')
            ],
            'options' => [
                'label' => $translator->translate('Codice Pin (4 cifre)')
            ]
        ]);

        $this->add([
            'name' => 'sesso',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'sesso'
            ],
            'options' => [
                'label' => $translator->translate('Titolo'),
                'value_options' => [
                    '0' => $translator->translate('Signore'),
                    '1' => $translator->translate('Signora')
                ]
            ]
        ]);

        $this->add([
            'name' => 'nome',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'nome',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Nome')
            ],
            'options' => [
                'label' => $translator->translate('Nome')
            ]
        ]);

        $this->add([
            'name' => 'cognome',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'cognome',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Cognome')
            ],
            'options' => [
                'label' => $translator->translate('Cognome')
            ]
        ]);

        $this->add([
            'name' => 'dataNascita',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => [
                'id' => 'dataNascita',
                'max' => date_create()->format('Y-m-d'),
                'placeholder' => $translator->translate('gg/mm/aaaa')
            ],
            'options' => [
                'label' => $translator->translate('Data di nascita')
            ]
        ]);

        $this->add([
            'name' => 'statoNascita',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'statoNascita'
            ],
            'options' => [
                'label' => $translator->translate('Stato di nascita'),
                'value_options' => $mondoService->getAllCountries()
            ]
        ]);

        $this->add([
            'name' => 'provinciaNascita',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'provinciaNascita',
                'placeholder' => $translator->translate('Provincia di nascita (EE = estero)')
            ],
            'options' => [
                'label' => $translator->translate('Provincia di nascita (EE = estero)'),
                'value_options' => $italiaService->getAllProvinces()
            ]
        ]);

        $this->add([
            'name' => 'cittaNascita',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'cittaNascita',
                'maxlength' => 32,
                'placeholder' => $translator->translate('Luogo di nascita')
            ],
            'options' => [
                'label' => $translator->translate('Comune di nascita'),
            ]
        ]);

        $this->add([
            'name' => 'resIndirizzo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'resIndirizzo',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Via e numero civico'),
            ],
            'options' => [
                'label' => $translator->translate('Via e numero civico'),
            ]
        ]);

        $this->add([
            'name' => 'resInfo',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'resInfo',
                'maxlength' => 64,
                'placeholder' => $translator->translate('Informazioni aggiuntive'),
            ],
            'options' => [
                'label' => $translator->translate('Informazioni aggiuntive'),
            ]
        ]);

        $this->add([
            'name' => 'resCap',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'resCap',
                'maxlength' => 12,
                'placeholder' => $translator->translate('CAP'),
            ],
            'options' => [
                'label' => $translator->translate('CAP'),
            ]
        ]);

        $this->add([
            'name' => 'resCitta',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'resCitta',
                'maxlength' => 16,
                'placeholder' => $translator->translate('Città'),
            ],
            'options' => [
                'label' => $translator->translate('Città'),
            ]
        ]);

        $this->add([
            'name' => 'lingua',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'lingua'
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
            'name' => 'cf',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'cf',
                'maxlength' => 16,
                'placeholder' => 'XXXXXXXXXXXXXXXX'
            ],
            'options' => [
                'label' => $translator->translate('Codice fiscale'),
            ]
        ]);

        $this->add([
            'name' => 'piva',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'piva',
                'maxlength' => 13,
                'placeholder' => 'ITNNNNNNNNNNN'
            ],
            'options' => [
                'label' => $translator->translate('Partita IVA (opzionale)'),
            ]
        ]);

        $this->add([
            'name' => 'cellulare',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'cellulare',
                'maxlength' => 13,
                'placeholder' => $translator->translate('Cellulare'),
            ],
            'options' => [
                'label' => $translator->translate('Cellulare'),
            ]
        ]);

        $this->add([
            'name' => 'telefono',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'telefono',
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

        $this->add([
            'name' => 'articolo1',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'articolo1'
            ],
            'options' => [
                'label' => $translator->translate('Condizioni generali di contratto'),
                'value_options' => [
                    '0' => $translator->translate('Accetto'),
                    '1' => $translator->translate('Non accetto')
                ]
            ]
        ]);

        $this->add([
            'name' => 'articolo2',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => [
                'id' => 'articolo2'
            ],
            'options' => [
                'label' => $translator->translate('Condizioni speciali di contratto'),
                'value_options' => [
                    '0' => $translator->translate('Accetto'),
                    '1' => $translator->translate('Non accetto')
                ]
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
                            'customerService' => $this->customerService
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
                            'min' => 8,
                            'max' => 16
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
                            'min' => 8,
                            'max' => 16
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
            'pin' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 4,
                            'max' => 4
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Digits'
                    ]
                ]
            ],
            'nome' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'cognome' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'dataNascita' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\EighteenDate'
                    ]
                ]
            ],
            'cittaNascita' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'statoNascita' => [
                'required' => true
            ],
            'provinciaNascita' => [
                'required' => true
            ],
            'resIndirizzo' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'resCap' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'resCitta' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'cf' => [
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
                            'customerService' => $this->customerService
                        ]
                    ]
                ]
            ],
            'piva' => [
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
            'cellulare' => [
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
            'telefono' => [
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
            'articolo1' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\OptionAccepted'
                    ]
                ]
            ],
            'articolo2' => [
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
