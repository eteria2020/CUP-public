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

class UserFieldset extends Fieldset implements InputFilterProviderInterface {

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
                    'male' => $translator->translate('Maschio'),
                    'female' => $translator->translate('Femmina')
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
            'name' => 'jobType',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'jobType'
            ],
            'options' => [
                'label' => $translator->translate('Professione'),
                'value_options' => [
                    " " => $translator->translate(" "),
                    "Imprenditore" => $translator->translate("Imprenditore"),
                    "Dipendente di azienda privata" => $translator->translate("Dipendente di azienda privata"),
                    "Dipendente di azienda partecipata" => $translator->translate("Dipendente di azienda partecipata"),
                    "Agente assicurativo" => $translator->translate("Agente assicurativo"),
                    "Agente di commercio" => $translator->translate("Agente di commercio"),
                    "Avvocato" => $translator->translate("Avvocato"),
                    "Notaio" => $translator->translate("Notaio"),
                    "Commercialista" => $translator->translate("Commercialista"),
                    "Dirigente" => $translator->translate("Dirigente"),
                    "Dirigente/Funzionario P.A./Ufficiale" => $translator->translate("Dirigente / Funzionario P.A. / Ufficiale"),
                    "Professore Universitario" => $translator->translate("Professore Universitario"),
                    "Altra libera professione" => $translator->translate("Altra libera professione"),
                    "Geometra" => $translator->translate("Geometra"),
                    "Architetto" => $translator->translate("Architetto"),
                    "Ingegnere" => $translator->translate("Ingegnere"),
                    "Medico" => $translator->translate("Medico"),
                    "Farmacista" => $translator->translate("Farmacista"),
                    "Artigiano" => $translator->translate("Artigiano"),
                    "Commerciante" => $translator->translate("Commerciante"),
                    "Studente" => $translator->translate("Studente"),
                    "Pensionato" => $translator->translate("Pensionato"),
                    "Casalinga" => $translator->translate("Casalinga"),
                    "Giornalista" => $translator->translate("Giornalista"),
                    "Consulente" => $translator->translate("Consulente"),
                    "Sportivo professionista" => $translator->translate("Sportivo professionista"),
                    "Artista" => $translator->translate("Artista"),
                    "Insegnante" => $translator->translate("Insegnante"),
                    "Politico" => $translator->translate("Politico"),
                    "Non occupato" => $translator->translate("Non occupato")
                ]
            ]
        ]);

        $this->add([
            'name' => 'howToKnow',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'howToKnow'
            ],
            'options' => [
                'label' => $translator->translate('Come hai conosciuto Sharengo?'),
                'value_options' => [
                    " " => $translator->translate(" "),
                    "Sito Sharengo" => $translator->translate("Sito Sharengo"),
                    "Motore di ricerca" => $translator->translate("Motore di ricerca"),
                    "Pubblicità online" => $translator->translate("Pubblicità online"),
                    "Macchine Sharengo" => $translator->translate("Macchine Sharengo"),
                    "Eventi" => $translator->translate("Eventi"),
                    "Consigliato dagli utenti" => $translator->translate("Consigliato dagli utenti")
                ]
            ]
        ]);

        $provinces = array_merge(
                [''], $provincesService->getAllProvinces()
        );

        $this->add([
            'name' => 'birthProvince',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'birthProvince',
                'class' => 'required'
            ],
            'options' => [
                'label' => $translator->translate('Provincia di nascita (EE = estero)'),
                'value_options' => $provinces,
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
                'maxlength' => 64,
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
                            'max' => 32
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
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd-m-Y'
                        ],
                        'break_chain_on_failure' => true
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
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/[A-Z]{2}/',
                            'message' => 'Il dato è richiesto e non può essere vuoto'
                        ]
                    ],
                    [
                        'name' => 'Application\Form\Validator\BirthProvince'
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
                        'name' => 'Application\Form\Validator\TaxCode',
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\DuplicateTaxCode',
                        'options' => [
                            'customerService' => $this->customersService
                        ],
                        'break_chain_on_failure' => true
                    ],
                    [
                        'name' => 'Application\Form\Validator\CoherentTaxCode'
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
                                \Zend\Validator\Callback::INVALID_VALUE => 'Il numero di telefono inserito non corrisponde a quello del codice di verifica'
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
                                \Zend\Validator\Callback::INVALID_VALUE => 'Il codice inserito non corrisponde a quello inviato'
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
            'jobType' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ]
            ],
            'howToKnow' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
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