<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Doctrine\ORM\EntityManager;
use Zend\Session\Container;

class MobileForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $userService;

    public function __construct(
        Translator $translator,
        AuthenticationService $userService,
        HydratorInterface $hydrator,
        EntityManager $entityManager
    ) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;

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
            ]
        ];
    }

    /**
     * persists the mobile in the database and returns the saved data
     *
     * @return Customers
     */
    public function saveData()
    {
        $customer = $this->getData();
        $customer->setMobile($customer->getMobile());
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $customer;
        
    }
}
