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

class OptionalFieldset extends Fieldset implements InputFilterProviderInterface {

    public function __construct(
        Translator $translator,
        HydratorInterface $hydrator
    ) {

        parent::__construct('optional', [
            'use_as_base_fieldset' => true
        ]);

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

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

        $this->add([
            'name' => 'vat',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'vat',
                'maxlength' => 13,
                'placeholder' => 'Partita IVA'
            ],
            'options' => [
                'label' => $translator->translate('Partita IVA'),
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'newsletter',
            'options' => [
                'label' => $translator->translate("Vorrei ricevere notizie e informazioni da Sharengo riguardanti la mia città. Posso annullare la mia iscrizione alla Newsletter in ogni momento tramite collegamento presente nelle e-mail e nelle Newsletter di Sharengo."),
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
            'newsletter' => [
                'required' => false,
            ],
        ];
    }

}