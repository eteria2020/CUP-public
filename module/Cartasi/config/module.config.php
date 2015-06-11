<?php

namespace Cartasi;

return [
    'router' => [
        'routes' => [
            'cartasi' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{cartasi}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'CartasiPayments',
                    ]
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'primo-pagamento' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{primo-pagamento}',
                            'defaults' => [
                                'action' => 'firstPayment'
                            ]
                        ]
                    ],
                    'ritorno-primo-pagamento' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{ritorno-primo-pagamento}',
                            'defaults' => [
                                'action' => 'returnFirstPayment'
                            ]
                        ]
                    ],
                    'rifutato-primo-pagamento' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{rifiutato-primo-pagamento}',
                            'defaults' => [
                                'action' => 'rejectedFirstPayment'
                            ]
                        ]
                    ],
                    'pagamento-ricorrente' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{pagamento-ricorrente}',
                            'defaults' => [
                                'action' => 'recurringPayment'
                            ]
                        ]
                    ],
                    'ritorno-pagamento-ricorrente' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{ritorno-pagamento-ricorrente}',
                            'defaults' => [
                                'action' => 'returnRecurringPayment'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Cartasi\Controller\CartasiPayments' => 'Cartasi\Controller\CartasiPaymentsController'
        ]
    ],
    'service_manager' => [
        'invokables' => [
            'Cartasi\Service\CartasiPayments' => 'Cartasi\Service\CartasiPaymentsService'
        ]
    ],
    'doctrine'        => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ],
            'orm_default'             => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ],
        ],
    ],
];