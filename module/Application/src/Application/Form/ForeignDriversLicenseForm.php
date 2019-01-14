<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Validator\Identical;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\File\MimeType;
use Zend\Mvc\I18n\Translator;

class ForeignDriversLicenseForm extends Form
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    /**
     * @var Translator $translator
     */
    private $translator;

    /**
     * ForeignDriversLicenseForm constructor.
     * @param EntityManager $entityManager
     * @param Translator $translator
     */
    public function __construct(EntityManager $entityManager, Translator $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;

        parent::__construct('foreign-drivers-license-form');
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    private function addElements()
    {
        $this->add([
            'name' => 'signature',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => [
                'id' => 'signature'
            ],
            'options' => [
                'use_hidden_element' => true,
                'checked_value' => 'true',
                'unchecked_value' => 'false'
            ]
        ]);

        $this->add([
            'name' => 'drivers-license-file',
            'type' => 'Zend\Form\Element\File',
            'attributes' => [
                'id' => 'drivers-license-file',
                'multiple' => true
            ]
        ]);
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFactory = new InputFactory();

        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'signature',
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'true',
                            'messages' => [
                                Identical::NOT_SAME => $this->translator->translate("E\' necessario confermare e sottoscrivere la dichiarazione"),
                            ],
                        ],
                    ],
                ]
            ])
        );
        $inputFilter->add(
            $inputFactory->createInput([
                'name' => 'drivers-license-file',
                'validators' => [
                    [
                        'name' => 'File/MimeType',
                        'options' => [
                            'mimeType' => 'image,application/pdf',
                            'messages' => [
                                MimeType::FALSE_TYPE => $this->translator->translate("Il file caricato ha un formato non valido; sono accettati solo formati di immagini e pdf"),
                                MimeType::NOT_DETECTED => $this->translator->translate("Non è stato possibile verificare il formato del file"),
                                MimeType::NOT_READABLE => $this->translator->translate("Il file caricato non è leggibile o non esiste")
                            ]
                        ]
                    ]
                ]
            ])
        );

        $this->setInputFilter($inputFilter);
    }
}
