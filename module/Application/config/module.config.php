<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'router_class' => 'Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack',
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'carsharing' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{carsharing}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'carsharing',
                    ),
                ]
            ],
            'cosae' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{cosa-e-sharengo}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'cosae',
                    ),
                ]
            ],
            'quantocosta' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{quantocosta}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'quantocosta',
                    ),
                ]
            ],
            'comefunziona' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{comefunziona}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'comefunziona',
                    ),
                ]
            ],
            'faq' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{faq}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'faq',
                    ),
                ]
            ],
            'contatti' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{contatti}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'contatti',
                    ),
                ]
            ],
            'login' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{login}',
                    'defaults' => array(
                        '__NAMESPACE__' => null,
                        'controller' => 'zfcuser',
                        'action'     => 'login',
                    ),
                ]
            ],
            'logout' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{logout}',
                    'defaults' => [
                        '__NAMESPACE__' => null,
                        'controller' => 'zfcuser',
                        'action'     => 'logout',
                    ]
                ],
                'may_terminate' => true
            ],
            'forgot' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{forgot-password}',
                    'defaults' => [
                        '__NAMESPACE__' => null,
                        'controller' => 'goalioforgotpassword_forgot',
                        'action' => 'forgot'
                    ]
                ],
                'may_terminate' => true
            ],
            'reset' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/{reset-password}/:userId/:token',
                    'defaults' => array(
                        '__NAMESPACE__' => null,
                        'controller' => 'goalioforgotpassword_forgot',
                        'action'     => 'reset',
                    ),
                    'constraints' => array(
                        'userId'  => '[A-Fa-f0-9]+',
                        'token' => '[A-F0-9]+',
                    ),
                ),
                'may_terminate' => true
            ),
            'signup' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup',
                    ),
                ]
            ],
            'signup-2' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-2}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action' => 'signup2'
                    ]
                ]
            ],
            'signup-3' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-3}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action' => 'signup3'
                    ]
                ]
            ],
            'signup-score' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-score}/:email',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup-score',
                    ),
                ]
            ],
            'signup-score-completion' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-score-completion}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup-score-completion',
                    ),
                ]
            ],
            'signup_insert' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-insert}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action' => 'signupinsert'
                    ]
                ]
            ],
            'cookies' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{cookies}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'cookies',
                    ),
                ]
            ],
            'notelegali' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{notelegali}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'notelegali',
                    ),
                ]
            ],
            'privacy' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{privacy}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'privacy',
                    ),
                ]
            ],
            'callcenter' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{callcenter}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'callcenter',
                    ),
                ]
            ],
            'eq-sharing' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{eq-sharing}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'eq-sharing',
                    ),
                ]
            ],
            'pay' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay}/:email',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay',
                    ),
                ]
            ],
            'pay-return' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay-return}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay-return',
                    ),
                ]
            ],
            'pay-error' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay-error}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay-error',
                    ),
                ]
            ],
            'pay-success' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay-success}',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay-success',
                    ),
                ]
            ],
            'area-utente' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{area-utente}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'action' => 'index',
                        'controller' => 'UserArea',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'tariffe' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{tariffe}',
                            'defaults' => [
                                'action' => 'rates'
                            ]
                        ]
                    ],
                    'pin' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{pin}',
                            'defaults' => [
                                'action' => 'pin'
                            ]
                        ]
                    ],
                    'rates-confirm' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/rates-confirm',
                            'defaults' => [
                                'action' => 'rates-confirm'
                            ]
                        ]
                    ],
                    'dati-pagamento' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/dati-pagamento',
                            'defaults' => [
                                'action' => 'dati-pagamento'
                            ]
                        ]
                    ],
                    'patente' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{patente}',
                            'defaults' => [
                                'action' => 'drivingLicence'
                            ]
                        ]
                    ],
                    'bonus' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{bonus}',
                            'defaults' => [
                                'action' => 'bonus'
                            ]
                        ]
                    ],
                    'additional-services' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{servizi-aggiuntivi}',
                            'defaults' => [
                                'action' => 'additional-services'
                            ]
                        ]
                    ],
                    'invoices-list' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{fatture}',
                            'defaults' => [
                                'action' => 'invoices-list'
                            ]
                        ]
                    ],
                    'rents' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{corse}',
                            'defaults' => [
                                'action' => 'rents'
                            ]
                        ]
                    ],
                    'activate-payments' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{attivazione}',
                            'defaults' => [
                                'action' => 'activate-payments'
                            ]
                        ]
                    ],
                ]
            ],
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => [
            'RegistrationService'      => 'Application\Service\RegistrationServiceFactory',
            'RegistrationForm'         => 'Application\Form\RegistrationFormFactory',
            'RegistrationForm2'        => 'Application\Form\RegistrationForm2Factory',
            'PaypalRequest'            => 'Application\Service\PaypalRequestFactory',
            'ProfilingPlatformService' => 'Application\Service\ProfilingPlatformServiceFactory',
            'PaymentService'           => 'Application\Service\PaymentServiceFactory',
            'ProfileForm'              => 'Application\Form\ProfileFormFactory',
            'PasswordForm'             => 'Application\Form\PasswordFormFactory',
            'DriverLicenseForm'        => 'Application\Form\DriverLicenseFormFactory',
            'PromoCodeForm'            => 'Application\Form\PromoCodeFormFactory'
        ],
        'invokables' => [
            'Application\Authentication\Adapter\Sharengo' => 'Application\Authentication\Adapter\Sharengo',
            'goalioforgotpassword_password_service' => 'Application\Service\PasswordService',
        ]
    ),
    'controllers' => [
        'factories' => [
            'Application\Controller\Index' => 'Application\Controller\IndexControllerFactory',
            'Application\Controller\User' => 'Application\Controller\UserControllerFactory',
            'Application\Controller\Payment' => 'Application\Controller\PaymentControllerFactory',
            'Application\Controller\UserArea' => 'Application\Controller\UserAreaControllerFactory',
            'Application\Controller\Console' => 'Application\Controller\ConsoleControllerFactory',
            'Application\Controller\RemoveGoldListTrips' => 'Application\Controller\RemoveGoldListTripsControllerFactory',
            'Application\Controller\ComputeTripsCost' => 'Application\Controller\ComputeTripsCostControllerFactory',
            'Application\Controller\ConsolePayments' => 'Application\Controller\ConsolePaymentsControllerFactory',
            'Application\Controller\Address' => 'Application\Controller\AddressControllerFactory',
            'Application\Controller\ConsolePayInvoice' => 'Application\Controller\ConsolePayInvoiceControllerFactory',
            'Application\Controller\ConsoleAccountCompute' => 'Application\Controller\ConsoleAccountComputeControllerFactory',
            'Application\Controller\EditTrip' => 'Application\Controller\EditTripControllerFactory',
            'Application\Controller\FixInvoicesBody' => 'Application\Controller\FixInvoicesBodyControllerFactory',
            'Application\Controller\ExportRegistries' => 'Application\Controller\ExportRegistriesControllerFactory'
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'CurrentRoute' => 'Application\View\Helper\CurrentRouteFactory',
            'LongLanguage' => 'Application\View\Helper\LongLanguageFactory',
            'Config' => 'Application\View\Helper\ConfigFactory'
        ],
        'invokables' => [
            'IsUserArea' => 'Application\View\Helper\IsUserArea',
            'Minute'     => 'Application\View\Helper\Minute',
            'IsLoggedIn' => 'Application\View\Helper\IsLoggedIn',
            'LoginProvider' => 'Application\View\Helper\LoginProvider'
        ]
    ],
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'zfc-user/user/login'     => __DIR__ . '/../view/zfc-user/user/login.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ),

    // ACL
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(

                array('controller' => 'zfcuser', 'roles' => array()),
                array('controller' => 'goalioforgotpassword_forgot', 'roles' => array()),
                array('controller' => 'Application\Controller\Index', 'roles' => array()),
                array('controller' => 'Application\Controller\Console', 'roles' => array()),
                array('controller' => 'Application\Controller\Payment', 'roles' => array()),
                array('controller' => 'Application\Controller\User', 'roles' => array()),
                array('controller' => 'Application\Controller\UserArea', 'roles' => array('user')),
                array('controller' => 'Cartasi\Controller\CartasiPayments', 'roles' => []),
                ['controller' => 'Application\Controller\RemoveGoldListTrips', 'roles' => []],
                ['controller' => 'Application\Controller\ComputeTripsCost', 'roles' => []],
                ['controller' => 'Application\Controller\ConsolePayments', 'roles' => []],
                ['controller' => 'Application\Controller\ConsolePayInvoice', 'roles' => []],
                ['controller' => 'Application\Controller\ConsoleAccountCompute', 'roles' => []],
                ['controller' => 'Application\Controller\Address', 'roles' => []],
                ['controller' => 'Application\Controller\EditTrip', 'roles' => []],
                ['controller' => 'Application\Controller\FixInvoicesBody', 'roles' => []],
                ['controller' => 'Application\Controller\ExportRegistries', 'roles' => []]
            ),
        ),
    ),

    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ]
        ]
    ],

    'console' => [
        'router' => [
            'routes' => [
                'get-discounts' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'get discounts',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'Console',
                            'action' => 'get-discounts'
                        ]
                    ]
                ],
                'assign-bonus' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'assign bonus',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'Console',
                            'action' => 'assign-bonus'
                        ]
                    ]
                ],
                'account-trips' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'account trips [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleAccountCompute',
                            'action' => 'account-trips'
                        ]
                    ]
                ],
                'account-trip' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'account trip <tripId> [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleAccountCompute',
                            'action' => 'account-trip'
                        ]
                    ]
                ],
                'account-user-trips' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'account trips user <customerId> [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleAccountCompute',
                            'action' => 'account-user-trips'
                        ]
                    ]
                ],
                'check-alarms' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'check alarms [--dry-run|-d] [--verbose|-v]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'Console',
                            'action' => 'check-alarms'
                        ]
                    ]
                ],
                'archive-reservations' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'archive reservations [--dry-run] [--verbose|-v]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'Console',
                            'action' => 'archive-reservations'
                        ]
                    ]
                ],
                'invoice-registrations' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'invoice registrations [--dry-run|-d] [--verbose|-v]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'Console',
                            'action' => 'invoice-registrations'
                        ]
                    ]
                ],
                'remove-gold' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'remove gold [--dry-run] [--verbose]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'RemoveGoldListTrips',
                            'action' => 'remove-gold-list-trips'
                        ]
                    ]
                ],
                'compute-trips-cost' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'compute trips cost [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ComputeTripsCost',
                            'action' => 'compute-trips-cost'
                        ]
                    ]
                ],
                'invoice-trips' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'invoice trips [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ComputeTripsCost',
                            'action' => 'invoice-trips'
                        ]
                    ]
                ],
                'disable-late-payers' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'disable late payers [--dry-run|-d] [--verbose|-v]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ComputeTripsCost',
                            'action' => 'disable-late-payers'
                        ]
                    ]
                ],
                'make-user-pay' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'make user pay <customerId> [--no-emails|-e] [--no-cartasi|-c] [--no-db|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsolePayments',
                            'action' => 'make-user-pay'
                        ]
                    ]
                ],
                'pay-invoice' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'pay invoice [--no-emails|-e] [--no-cartasi|-c] [--no-db|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsolePayInvoice',
                            'action' => 'pay-invoice'
                        ]
                    ]
                ],
                'account-compute' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'account compute [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleAccountCompute',
                            'action' => 'account-compute'
                        ]
                    ]
                ],
                'generate-locations' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'generate locations [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'Address',
                            'action' => 'generate-locations'
                        ]
                    ]
                ],
                'edit-trip' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'edit trip <tripId> [--notPayable] [--endDate=]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'EditTrip',
                            'action' => 'edit-trip'
                        ]
                    ]
                ],
                'fix-invoices-body' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'fix invoices body',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'FixInvoicesBody',
                            'action' => 'fix-invoices-body'
                        ]
                    ]
                ],
                'export-registries' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'export registries [--dry-run|-d] [--no-customers|-c] [--no-invoices|-i] [--ignore-exceptions|-e] [--all|-a]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ExportRegistries',
                            'action' => 'export-registries'
                        ]
                    ]
                ]
            ],
        ],
    ],
);
