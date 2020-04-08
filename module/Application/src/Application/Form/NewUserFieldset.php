<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;
use SharengoCore\Service\CustomersService;
use SharengoCore\Service\ProvincesService;
use SharengoCore\Service\FleetService;
use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Validator\Identical;
use Zend\Session\Container;
use Zend\Validator\Callback;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\NotEmpty;

class NewUserFieldset extends Fieldset implements InputFilterProviderInterface {

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var CustomersService
     */
    private $customersService;

    /**
     * @var FleetService
     */
    private $fleetService;

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator,
        CustomersService $customersService,
        FleetService $fleetService
    ) {
        $this->translator = $translator;
        $this->customersService = $customersService;
        $this->fleetService = $fleetService;

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
                'placeholder' => $this->translator->translate('Digita la tua email'),
                'class' => 'required',
                'autocomplete' => 'username'
            ],
            'options' => [
                'label' => $this->translator->translate('Email')
            ]
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'id' => 'password',
                'placeholder' => $this->translator->translate('Imposta la tua password'),
                'class' => 'required',
                'autocomplete' => 'current-password'
            ],
            'options' => [
                'label' => $this->translator->translate('Password')
            ]
        ]);

        $fleets = $fleetService->getFleetsSelectorArrayNoDummy(
            [0 => '---']
        );

        // MySharengo version //
        /*
        foreach($fleets as $key => $fleet) {
            if($fleet=='Modena') {
                unset($fleets[$key]);
            }
        }
        */
        // end MySharengo version //

        $this->add([
            'name' => 'fleet',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'fleet'
            ],
            'options' => [
                'value_options' => $fleets
            ]
        ]);
        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'privacyCondition',
            'options' => [
                'label' => $this->translator->translate("Accetto espressamente i Termini e le Condizioni Privacy "),
                'use_hidden_element' => true,
                'checked_value' => 'on',
                'unchecked_value' => 'off',
            ],
            'attributes' => [
                'value' => 'off'
            ]
        ]);
        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'newsletter',
            'options' => [
                'label' => $translator->translate("Accetto di ricevere comunicazioni cartacee e digitali relative al servizio, ai prodotti, ai vantaggi offerti da Sharengo e dai Partner Sharengo (facoltativo)."),
                'use_hidden_element' => true,
                'checked_value' => 'on',
                'unchecked_value' => 'off',
            ],
            'attributes' => [
                'value' => 'off'
            ]
        ]);
        $this->add([
            'name' => 'profilingCounter',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'profilingCounter'
            ]
        ]);
        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'generalCondition1',
            'options' => [
                'label' => $translator->translate("Autorizzo il trattamento dei miei dati personali per finalità assicurative, antifrode e prevenzione sinistri (facoltativo)."),
                'use_hidden_element' => true,
                'checked_value' => 'on',
                'unchecked_value' => 'off',
            ],
            'attributes' => [
                'value' => 'off'
            ]
        ]);
        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'generalCondition2',
            'options' => [
                'label' => $this->translator->translate('Il Cliente, dopo aver preso visione dei Termini e Condizioni Contrattuali che regolano il rapporto, dichiara ai sensi degli articoli 1341 e 1342 del Codice Civile Italiano, di accettare integralmente e specificatamente le clausole dei seguenti articoli: 3 (oggetto e parti del contratto), 4 (modifica unilaterale del Contratto), 5 (iscrizione e prenotazione online), 6 (tariffe e fatturazione), 7 (divieto di sublocazione e di cessione), 8 (esonero di responsabilità), 9 (permesso di guida), 10 (utilizzo dei veicoli, clausola risolutiva espressa), 11 (fine del noleggio), 12 (sinistri e avaria del veicolo) 13 (sanzioni in materia di circolazione stradale), 14 (assicurazioni), 16 (decorrenza, durata, rinnovo, sospensione, recesso, risoluzione del contratto), 17 (reclami), 18 (penali), 20 (foro competente).'),
                'use_hidden_element' => true,
                'checked_value' => 'on',
                'unchecked_value' => 'off',
            ],
            'attributes' => [
                'value' => 'off'
            ]
        ]);
    }

    public function getInputFilterSpecification() {

        return [
            'email' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [NotEmpty::IS_EMPTY => $this->translator->translate('Il valore è richiesto e non può essere vuoto')]
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateEmail',
                        'options' => [
                            'customerService' => $this->customersService
                        ]
                    ],
                    [
                        'name' => 'SharengoCore\Form\Validator\DisposableEmail'
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
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [NotEmpty::IS_EMPTY => $this->translator->translate('Il valore è richiesto e non può essere vuoto')]
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\Password',
                    ]
                ]
            ],
            'privacyCondition' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'on',
                            'messages' => [
                                Identical::NOT_SAME => $this->translator->translate("Il dato è richiesto per procedere"),
                            ]
                        ],
                    ],
                ]
            ],
            'fleet' => [
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\ValidFleet',
                        'options' => [
                            'fleetService' => $this->fleetService
                        ]
                    ]
                ]
            ],
            'generalCondition1' => [
                'required' => false,
            ],
            'generalCondition2' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'on',
                            'messages' => [
                                Identical::NOT_SAME => $this->translator->translate("Il dato è richiesto per procedere"),
                            ]
                        ],
                    ],
                ]
            ],
            'newsletter' => [
                'required' => false,
            ],
        ];
    }

}