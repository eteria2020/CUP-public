<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Doctrine\ORM\EntityManager;
use Zend\Session\Container;

use SharengoCore\Entity\Customers;
use SharengoCore\Service\CountriesService;

class MobileForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $userService;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CountriesService
     */
    private $countriesService;

    public function __construct(
        Translator $translator,
        AuthenticationService $userService,
        HydratorInterface $hydrator,
        EntityManager $entityManager,
        CountriesService $countriesService
    ) {
        $this->translator = $translator;
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->countriesService = $countriesService;

        $this->setHydrator($hydrator);
        $this->setObject(new Customers());

        parent::__construct('profile-form');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => [
                'id' => 'id'
            ]
        ]);

        $this->add([
            'name' => 'dialCode',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => [
                'id' => 'dialCode'
            ],
            'options' => [
                'label' => $translator->translate('Prefisso internazionale'),
                'value_options' => $countriesService->getAllPhoneCodeByCountry()
            ]
        ]);

        $this->add([
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'mobile',
                'maxlength' => 13,
                'placeholder' => 'Cellulare',
            ],
            'options' => [
                'label' => $this->translator->translate('Cellulare'),
            ]
        ]);
        
        $this->add([
            'name' => 'smsCode',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => [
                'id' => 'smsCode',
                'maxlength' => 4,
                'placeholder' => 'xxxx',
            ],
            'options' => [
                'label' => $this->translator->translate('Codice Sms'),
            ]
        ]);
        
    }

    public function getInputFilterSpecification()
    {
        return [
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
                                \Zend\Validator\Callback::INVALID_VALUE => $this->translator->translate('Il numero di telefono inserito non corrisponde a quello del codice di verifica')
                            ],
                            'callback' => function($value, $context = array()) {
                                $smsVerification = new Container('smsVerification');
                               
                                if(is_null($smsVerification->offsetGet('mobile'))){
                                    $isValid = true;
                                    return $isValid;
                                }else{
                                    $isValid = $value == $smsVerification->offsetGet('mobile');
                                    return $isValid;
                                }
                            }
                        ]
                    ]
                ]
            ],
            'dialCode' => [
                'required' => true
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
                                \Zend\Validator\Callback::INVALID_VALUE => $this->translator->translate('Il codice inserito non corrisponde a quello inviato')
                            ],
                            'callback' => function($value, $context = array()) {

                                $smsVerification = new Container('smsVerification');

                                if(is_null($smsVerification->offsetGet('code'))){
                                    $isValid = true;
                                    return $isValid;
                                }else{
                                    $isValid = $value == $smsVerification->offsetGet('code');
                                    return $isValid;
                                }
                            }
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * persists the mobile in the database and returns the saved data
     *
     * @return array|object
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveData()
    {
        $smsVerification = new Container('smsVerification');
        $dialCode = $smsVerification->offsetGet('dialCode');
        $customer = $this->getData();
        $customer->setMobile("+".$dialCode.$customer->getMobile());
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $customer;
        
    }
}
