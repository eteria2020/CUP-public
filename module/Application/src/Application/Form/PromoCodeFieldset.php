<?php

namespace Application\Form;

use SharengoCore\Service\PromoCodesService;

use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;

class PromoCodeFieldset extends Fieldset implements InputFilterProviderInterface
{
    private $promoCodesService;

    public function __construct(
        Translator $translator,
        PromoCodesService $promoCodesService
    ) {
        $this->promoCodesService = $promoCodesService;

        parent::__construct('promocode', [
            'use_as_base_fieldset' => false
        ]);

        $this->add([
            'name' => 'promocode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'maxlength' => 5,
                'placeholder' => $translator->translate('Promo code'),
            ]
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'promocode' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\PromoCode',
                        'options' => [
                            'promoCodesService' => $this->promoCodesService
                        ]
                    ]
                ]
            ],
        ];
    }
}
