<?php

namespace Application\Form;

use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\PromoCodesService;

use Zend\Form\Fieldset;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterProviderInterface;

class PromoCodeFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var PromoCodesService
     */
    private $promoCodesService;

    /**
     * @var CarrefourService|null
     */
    private $carrefourService;

    /**
     * @param Translator $translator
     * @param PromoCodesService $promoCodesService
     * @param CarrefourService|null $carrefourService
     */
    public function __construct(
        Translator $translator,
        PromoCodesService $promoCodesService,
        CarrefourService $carrefourService = null
    ) {
        $this->promoCodesService = $promoCodesService;
        $this->carrefourService = $carrefourService;

        parent::__construct('promocode', [
            'use_as_base_fieldset' => false
        ]);

        $this->add([
            'name' => 'promocode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'name',
                'maxlength' => 24,
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
                            'promoCodesService' => $this->promoCodesService,
                            'carrefourService' => $this->carrefourService
                        ]
                    ]
                ]
            ],
        ];
    }
}
