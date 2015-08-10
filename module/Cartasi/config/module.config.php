<?php

namespace Cartasi;

return [
    'router' => [
        'routes' => [
            'cartasi' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/cartasi',
                    'defaults' => [
                        '__NAMESPACE__' => 'Cartasi\Controller',
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
                    'rifiutato-primo-pagamento' => [
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
                            'route' => '/pagamento-ricorrente',
                            'defaults' => [
                                'action' => 'recurringPayment'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            'Cartasi\Controller\CartasiPayments' => 'Cartasi\Controller\CartasiPaymentsControllerFactory'
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Cartasi\Service\CartasiPayments' => 'Cartasi\Service\CartasiPaymentsServiceFactory',
            'Cartasi\Service\CartasiContracts' => 'Cartasi\Service\CartasiContractsServiceFactory'
        ]
    ],
    'doctrine'        => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity']
            ],
            'orm_default'             => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ],
];
