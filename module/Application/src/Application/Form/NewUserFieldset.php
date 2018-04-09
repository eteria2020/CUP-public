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
    Translator $translator, HydratorInterface $hydrator, CountriesService $countriesService, CustomersService $customersService, ProvincesService $provincesService, FleetService $fleetService
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
            'name' => 'generalCondition1',
            'options' => [
                'label' => $translator->translate("Ho letto, compreso e accettato i Termini e Condizioni Generali di Contratto e il Regolamento Tariffario del servizio di car sharing SHARE’NGO®, fornito da C.S. Group S.p.A. e dalle sue controllate: C.S. Firenze S.r.l., C.S. Milano S.r.l. e C.S. Roma S.r.l."),
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
                'label' => $translator->translate('Dichiaro ai sensi e per gli effetti di cui agli artt. 1341 e 1342 c.c., avendo letto i presenti Termini e Condizioni Generali di Contratto di accettare espressamente e approvare specificatamente le condizioni di cui agli articoli: 3 (oggetto e parti del contratto), 4 (modifica unilaterale del Contratto), 5 (iscrizione e prenotazione online del Car Sharing SHARE’NGO®), 6 (tariffe e fatturazione), 7 (divieto di sublocazione e di cessione), 8 (esonero di responsabilità), 9 (permesso di guida), 10 (utilizzo dei veicoli. Clausola risolutiva espressa), 11 (sinistro o avaria del veicolo), 12 (furti e vandalismi), 13 (sanzioni in materia di circolazione stradale), 14 (assicurazioni), 16 (decorrenza, durata, rinnovo, sospensione, recesso, risoluzione del contratto), 17 (reclami), 18 (penali), 20 (foro competente).'),
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
            'name' => 'privacyCondition',
            'options' => [
                'label' => $translator->translate("Ho letto, compreso e accettato l’Informativa Privacy per i Clienti SHARE’NGO® ed acconsento al trattamento dei miei dati personali secondo le modalità indicate "),
                'use_hidden_element' => true,
                'checked_value' => 'on',
                'unchecked_value' => 'off',
            ],
            'attributes' => [
                'value' => 'off'
            ]
        ]);
        //Add field to registration form privacyInformation, type checkbox
        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'privacyInformation',
            'options' => [
                'label' => $translator->translate("Al fine di migliorare il servizio ed essere aggiornato sulle offerte di SHARE’NGO® e dei partner di SHARE’NGO® riservate in via preferenziale e/o esclusiva ai clienti SHARE’NGO®, do il mio consenso a ricevere comunicazioni di SHARE’NGO® via email, SMS o posta, inclusi gli inviti a partecipare a indagini di mercato e sondaggi."),
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
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 8
                        ]
                    ]
                ]
            ],
            'generalCondition1' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'on',
                            'messages' => [
                                Identical::NOT_SAME => "Value is required and can't be empty",
                            ]
                        ],
                    ],
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
                                Identical::NOT_SAME => "Value is required and can't be empty",
                            ]
                        ],
                    ],
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
                                Identical::NOT_SAME => "Value is required and can't be empty",
                            ]
                        ],
                    ],
                ]
            ],
            //Validation specifications to checkbox privacyInformation
            'privacyInformation' => [
                'required' => false
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
            ]
        ];
    }

}