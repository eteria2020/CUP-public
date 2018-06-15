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

class NewUserFieldset extends Fieldset implements InputFilterProviderInterface {

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
                'placeholder' => 'Digita la tua email',
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
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'privacyCondition',
            'options' => [
                'label' => $translator->translate("Do il mio consenso al trattamento dei miei dati personali per comunicazioni sia cartacee che digitali relative al servizio, ai prodotti e alle promozioni Sharengo."),
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
            'name' => 'generalCondition2',
            'options' => [
                'label' => $translator->translate('Il Cliente, dopo aver preso visione  dei Termini e Condizioni Contrattuali che regolano il rapporto, dichiara ai sensi degli articoli 1341 e 1342 del Codice Civile Italiano, di accettare integralmente e specificatamente le clausole dei seguenti articoli: 3 (oggetto e parti del contratto), 4 (modifica unilaterale del Contratto), 5 (iscrizione e prenotazione online del Car Sharing Sharengo), 6 (tariffe e fatturazione), 7 (divieto di sublocazione e di cessione), 8 (esonero di responsabilità), 9 (permesso di guida), 10 (utilizzo dei veicoli. Clausola risolutiva espressa), 11 (sinistro o avaria del veicolo), 12 (furti e vandalismi), 13 (sanzioni in materia di circolazione stradale), 14 (assicurazioni), 16 (decorrenza, durata, rinnovo, sospensione, recesso, risoluzione del contratto), 17 (reclami), 18 (penali), 20 (foro competente).'),
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
            'password' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
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
                                Identical::NOT_SAME => "Il dato è richiesto per procedere",
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
            'generalCondition2' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'on',
                            'messages' => [
                                Identical::NOT_SAME => "Il dato è richiesto per procedere",
                            ]
                        ],
                    ],
                ]
            ],
        ];
    }

}