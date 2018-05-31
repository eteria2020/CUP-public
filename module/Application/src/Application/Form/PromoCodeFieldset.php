<?php

namespace Application\Form;

use SharengoCore\Service\CarrefourService;
use SharengoCore\Service\PromoCodesService;
use SharengoCore\Service\PromoCodesOnceService;
use SharengoCore\Service\PromoCodesMemberGetMemberService;

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
     * @var PromoCodesOnceService
     */
    private $promoCodesOnceService;

    /**
     * @var CarrefourService|null
     */
    private $carrefourService;

    /**
     * @var PromoCodesMemberGetMemberService
     */
    private $promoCodesMemberGetMemberService;

    /**
     * @param Translator $translator
     * @param PromoCodesService $promoCodesService
     * @param PromoCodesOnceService $promoCodesOnceService
     * @param CarrefourService|null $carrefourService
     * @param PromoCodesMemberGetMemberService $promoCodesMemberGetMemberService
     */
    public function __construct(
        Translator $translator,
        PromoCodesService $promoCodesService,
        PromoCodesOnceService $promoCodesOnceService,
        CarrefourService $carrefourService = null,
        PromoCodesMemberGetMemberService $promoCodesMemberGetMemberService
    ) {
        $this->promoCodesService = $promoCodesService;
        $this->promoCodesOnceService = $promoCodesOnceService;
        $this->carrefourService = $carrefourService;
        $this->promoCodesMemberGetMemberService = $promoCodesMemberGetMemberService;

        parent::__construct('promocode', [
            'use_as_base_fieldset' => false
        ]);

        $this->add([
            'name' => 'promocode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'promocode',
                'maxlength' => 24,
                'placeholder' => $translator->translate('Promo code'),
                'oninput'=>'this.value=this.value.toUpperCase()'
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
                            'promoCodesOnceService' => $this->promoCodesOnceService,
                            'carrefourService' => $this->carrefourService,
                            'promoCodesMemberGetMemberService' => $this->promoCodesMemberGetMemberService,
                        ]
                    ]
                ]
            ],
        ];
    }
}
