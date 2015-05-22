<?php

namespace Application\Form;

use SharengoCore\Entity\Customers;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\I18n\Translator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Doctrine\ORM\EntityManager;

class PasswordForm extends Form implements InputFilterProviderInterface
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
            'name' => 'oldPassword',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'id' => 'password',
                'maxlength' => 16,
                'placeholder' => '********'

            ],
            'options' => [
                'label' => $translator->translate('Vecchia password')
            ]
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'id' => 'password',
                'maxlength' => 16,
                'placeholder' => '********'

            ],
            'options' => [
                'label' => $translator->translate('Nuova password')
            ]
        ]);

        $this->add([
            'name' => 'password2',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => [
                'id' => 'password2',
                'maxlength' => 16,
                'placeholder' => '********'

            ],
            'options' => [
                'label' => $translator->translate('Ripeti password')
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'oldPassword' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Application\Form\Validator\CurrentUserPassword',
                        'options' => [
                            'userService' => $this->userService
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
                            'min' => 8,
                            'max' => 16
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
                            'min' => 8,
                            'max' => 16
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
            ]
        ];
    }

    /**
     * persists the password in the database and returns the saved data
     *
     * @return Customers
     */
    public function saveData()
    {
        $customer = $this->getData();
        $customer->setPassword(hash("MD5", $customer->getPassword()));
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $customer;
    }
}
