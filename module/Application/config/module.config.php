<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'router' => [
        'router_class' => 'Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack',
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'zone' => [
                'type'    => 'Literal',
                'options' => [
                    'route' => '/zone',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'get-list-zones',
                    ],
                ],
            ],
            'carsharing' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{carsharing}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'carsharing',
                    ],
                ]
            ],
            'cosae' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{cosa-e-sharengo}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'cosae',
                    ],
                ]
            ],
            'quantocosta' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{quantocosta}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'quantocosta',
                    ],
                ]
            ],
            'comefunziona' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{comefunziona}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'comefunziona',
                    ],
                ]
            ],
            'faq' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{faq}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'faq',
                    ],
                ]
            ],
            'contatti' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{contatti}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'contatti',
                    ],
                ]
            ],
            'login' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{login}',
                    'defaults' => [
                        '__NAMESPACE__' => null,
                        'controller' => 'zfcuser',
                        'action'     => 'login',
                    ],
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
            'reset' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{reset-password}/:userId/:token',
                    'defaults' => [
                        '__NAMESPACE__' => null,
                        'controller' => 'goalioforgotpassword_forgot',
                        'action'     => 'reset',
                    ],
                    'constraints' => [
                        'userId'  => '[A-Fa-f0-9]+',
                        'token' => '[A-F0-9]+',
                    ],
                ],
                'may_terminate' => true
            ],
            'signup' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup',
                    ],
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
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup-score',
                    ],
                ]
            ],
            'signup-score-completion' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-score-completion}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup-score-completion',
                    ],
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
            'promocode-signup' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{promocode-signup}/:promocode',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action' => 'promocode-signup'
                    ]
                ]
            ],
            'foreign-drivers-license' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{foreign-drivers-license}[/:hash]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'ForeignDriversLicense',
                        'action' => 'foreign-drivers-license'
                    ]
                ]
            ],
            'foreign-drivers-license-completion' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{foreign-drivers-license-completion}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'ForeignDriversLicense',
                        'action' => 'completion'
                    ]
                ]
            ],
            'cookies' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{cookies}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'cookies',
                    ],
                ]
            ],
            'notelegali' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{notelegali}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'notelegali',
                    ],
                ]
            ],
            'privacy' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{privacy}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'privacy',
                    ],
                ]
            ],
            'callcenter' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{callcenter}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'callcenter',
                    ],
                ]
            ],
            'anas' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{anas}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'anas',
                    ],
                ],
            ],
            'bikemi' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{bikemi}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',// Redirect to the Index page as require on Issue-1295
                        'action'     => 'index',
                    ],
                ]
            ],
            'eq-sharing' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{eq-sharing}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ],
                ]
            ],
            'fao' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{fao}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'fao',
                    ],
                ]
            ],
            'ordpro' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{ordpro}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'ordpro',
                    ],
                ],
            ],
            'teatro-elfo' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{elfo}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'teatro-elfo',
                    ],
                ]
            ],
            'linear' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{linear}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'linear',
                    ],
                ]
            ],
            'firenze' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{firenze}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'firenze',
                    ],
                ]
            ],
            'volontariocard' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{volontariocard}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'volontariocard',
                    ],
                ]
            ],
            'legambiente' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{legambiente}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'legambiente',
                    ],
                ],
            ],
            'aeronautica' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{isma}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'aeronautica',
                    ],
                ]
            ],
            'roma' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{roma}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'roma',
                    ],
                ]
            ],
            'saba' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{saba}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'saba',
                    ],
                ]
            ],
            'express' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{express}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'express',
                    ],
                ],
            ],
            'acea' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{acea}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'acea',
                    ],
                ],
            ],
            'unirm1' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{unirm1}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'unirm1',
                    ],
                ],
            ],
            'pay' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay}/:email',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay',
                    ],
                ]
            ],
            'pay-return' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay-return}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay-return',
                    ],
                ]
            ],
            'pay-error' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay-error}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay-error',
                    ],
                ]
            ],
            'pay-success' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{pay-success}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Payment',
                        'action'     => 'pay-success',
                    ],
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
                    'utente' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{utente}',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'CustomerController',
                                'action' => 'customer-data'
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
                                'controller' => 'AdditionalServices',
                                'action' => 'additional-services'
                            ]
                        ]
                    ],
                    'bonus-package' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/package/:id',
                            'defaults' => [
                                'controller' => 'CustomerBonusPackages',
                                'action' => 'package'
                            ]
                        ]
                    ],
                    'buy-package' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'buy-package',
                            'defaults' => [
                                'controller' => 'CustomerBonusPackages',
                                'action' => 'buy-package'
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
                    'send-discount-request' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/send-discount-request',
                            'defaults' => [
                                'action' => 'send-discount-request'
                            ],
                        ],
                    ],
                    'discount-status' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/discount-status[/:id]',
                            'defaults' => [
                                'controller' => 'DiscountStatus',
                                'action' => null
                            ]
                        ]
                    ]
                ],
            ],
            'scn-social-auth-user' => [
                'child_routes' => [
                    'authenticate' => [
                        'child_routes' => [
                            'provider' => [
                                'options' => [
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Application\Controller',
                                        'controller' => 'SocialAuthController',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'thank-you' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{thank-you}',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'SocialAuthController',
                                'action' => 'thank-you'
                            ]
                        ]
                    ],
                    'register' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{register}/:id',
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'SocialAuthController',
                                'action' => 'register'
                            ]
                        ]
                    ]
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service'
        ],
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
            'PromoCodeForm'            => 'Application\Form\PromoCodeFormFactory',
            'ForeignDriversLicenseForm' => 'Application\Form\ForeignDriversLicenseFormFactory',
            'Application\Service\ProviderAuthentication' => 'Application\Service\ProviderAuthenticationServiceFactory',
            'Application\Listener\DriversLicenseValidationListener' => 'Application\Listener\DriversLicenseValidationListenerFactory',
            'Application\Listener\DriversLicensePostValidationLogger' => 'Application\Listener\DriversLicensePostValidationLoggerFactory',
            'Application\Listener\DriversLicensePostValidationListener' => 'Application\Listener\DriversLicensePostValidationListenerFactory',
            'Application\Listener\DriversLicensePostValidationNotifier' => 'Application\Listener\DriversLicensePostValidationNotifierFactory',
            'Application\Listener\DriversLicenseEditingListener' => 'Application\Listener\DriversLicenseEditingListenerFactory',
            'Application\Listener\ProviderAuthenticatedCustomerRegistered' => 'Application\Listener\ProviderAuthenticatedCustomerRegisteredFactory',
            'Application\Listener\SuccessfulPaymentListener' => 'Application\Listener\SuccessfulPaymentListenerFactory',
        ],
        'invokables' => [
            'Application\Authentication\Adapter\Sharengo' => 'Application\Authentication\Adapter\Sharengo',
            'goalioforgotpassword_password_service' => 'Application\Service\PasswordService',
        ]
    ],
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
            'Application\Controller\FixRegistrationInvoicesAmount' => 'Application\Controller\FixRegistrationInvoicesAmountControllerFactory',
            'Application\Controller\ExportRegistries' => 'Application\Controller\ExportRegistriesControllerFactory',
            'Application\Controller\GenerateExtraInvoices' => 'Application\Controller\GenerateExtraInvoicesControllerFactory',
            'Application\Controller\CustomerBonusPackages' => 'Application\Controller\CustomerBonusPackagesControllerFactory',
            'Application\Controller\GeneratePackageInvoices' => 'Application\Controller\GeneratePackageInvoicesControllerFactory',
            'Application\Controller\DriversLicenseValidation' => 'Application\Controller\DriversLicenseValidationControllerFactory',
            'Application\Controller\GenerateTripInvoice' => 'Application\Controller\GenerateTripInvoiceControllerFactory',
            'Application\Controller\ForeignDriversLicense' => 'Application\Controller\ForeignDriversLicenseControllerFactory',
            'Application\Controller\SocialAuthController' => 'Application\Controller\SocialAuthControllerFactory',
            'Application\Controller\DisableCustomerController' => 'Application\Controller\DisableCustomerControllerFactory',
            'Application\Controller\DisableOldDiscountsController' => 'Application\Controller\DisableOldDiscountsControllerFactory',
            'Application\Controller\DiscountStatus' => 'Application\Controller\DiscountStatusControllerFactory',
            'Application\Controller\AdditionalServices' => 'Application\Controller\AdditionalServicesControllerFactory',
        ],
        'invokables' => [
            'Application\Controller\LandingPage' => 'Application\Controller\LandingPageController',
            'Application\Controller\CustomerController' => 'Application\Controller\CustomerController',
        ]
    ],
    'view_helpers' => [
        'factories' => [
            'CurrentRoute' => 'Application\View\Helper\CurrentRouteFactory',
            'LongLanguage' => 'Application\View\Helper\LongLanguageFactory',
            'Config' => 'Application\View\Helper\ConfigFactory',
            'availableFleets' => 'Application\View\Helper\AvailableFleetsFactory'
        ],
        'invokables' => [
            'IsUserArea' => 'Application\View\Helper\IsUserArea',
            'Minute'     => 'Application\View\Helper\Minute',
            'IsLoggedIn' => 'Application\View\Helper\IsLoggedIn',
            'LoginProvider' => 'Application\View\Helper\LoginProvider'
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'zfc-user/user/login'     => __DIR__ . '/../view/zfc-user/user/login.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ],

    // ACL
    'bjyauthorize' => [
        'guards' => [
            'BjyAuthorize\Guard\Controller' => [

                ['controller' => 'zfcuser', 'roles' => []],
                ['controller' => 'goalioforgotpassword_forgot', 'roles' => []],
                ['controller' => 'Application\Controller\Index', 'roles' => []],
                ['controller' => 'Application\Controller\Console', 'roles' => []],
                ['controller' => 'Application\Controller\Payment', 'roles' => []],
                ['controller' => 'Application\Controller\User', 'roles' => []],
                ['controller' => 'Application\Controller\UserArea', 'roles' => ['user']],
                ['controller' => 'Cartasi\Controller\CartasiPayments', 'roles' => []],
                ['controller' => 'Application\Controller\RemoveGoldListTrips', 'roles' => []],
                ['controller' => 'Application\Controller\ComputeTripsCost', 'roles' => []],
                ['controller' => 'Application\Controller\ConsolePayments', 'roles' => []],
                ['controller' => 'Application\Controller\ConsolePayInvoice', 'roles' => []],
                ['controller' => 'Application\Controller\ConsoleAccountCompute', 'roles' => []],
                ['controller' => 'Application\Controller\Address', 'roles' => []],
                ['controller' => 'Application\Controller\EditTrip', 'roles' => []],
                ['controller' => 'Application\Controller\FixInvoicesBody', 'roles' => []],
                ['controller' => 'Application\Controller\FixRegistrationInvoicesAmount', 'roles' => []],
                ['controller' => 'Application\Controller\ExportRegistries', 'roles' => []],
                ['controller' => 'Application\Controller\LandingPage', 'roles' => []],
                ['controller' => 'Application\Controller\GenerateExtraInvoices', 'roles' => []],
                ['controller' => 'Application\Controller\CustomerBonusPackages', 'roles' => []],
                ['controller' => 'Application\Controller\GeneratePackageInvoices', 'roles' => []],
                ['controller' => 'Application\Controller\DriversLicenseValidation', 'roles' => []],
                ['controller' => 'Application\Controller\GenerateTripInvoice', 'roles' => []],
                ['controller' => 'Application\Controller\ForeignDriversLicense', 'roles' => []],
                ['controller' => 'ScnSocialAuth-User', 'roles' => []],
                ['controller' => 'ScnSocialAuth-HybridAuth', 'roles' => []],
                ['controller' => 'Application\Controller\SocialAuthController', 'roles' => []],
                ['controller' => 'Application\Controller\DisableCustomerController', 'roles' => []],
                ['controller' => 'Application\Controller\DisableOldDiscountsController', 'roles' => []],
                ['controller' => 'Application\Controller\CustomerController', 'roles' => []],
                ['controller' => 'Application\Controller\DiscountStatus', 'roles' => []],
                ['controller' => 'Application\Controller\AdditionalServices', 'roles' => ['user']],
            ],
        ],
    ],

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
                'compute-trip-cost' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'compute trip cost <tripId> [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ComputeTripsCost',
                            'action' => 'compute-trip-cost'
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
                'generate-trip-invoice' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'generate trip invoice <tripPaymentId>',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'GenerateTripInvoice',
                            'action' => 'generate-invoice'
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
                'fix-registration-invoices-amount' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'fix registration invoices amount',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'FixRegistrationInvoicesAmount',
                            'action' => 'fix-registration-invoices-amount'
                        ]
                    ]
                ],
                'export-registries' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'export registries [--dry-run|-d] [--no-customers|-c] [--no-invoices|-i] [--all|-a] [--no-ftp|-f] [--test-name|-t] [--date=] [--fleet=]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ExportRegistries',
                            'action' => 'export-registries'
                        ]
                    ]
                ],
                'generate-extra-invoices' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'generate extra invoices [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'GenerateExtraInvoices',
                            'action' => 'generate-extra-invoices'
                        ]
                    ]
                ],
                'generate-package-invoices' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'generate package invoices [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'GeneratePackageInvoices',
                            'action' => 'generate-package-invoices'
                        ]
                    ]
                ],
                'drivers-license-validation' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'validate drivers licenses',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'DriversLicenseValidation',
                            'action' => 'validate-drivers-license'
                        ]
                    ]
                ],
                'disable-customer-drivers-license' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'disable customer drivers license <customerId>',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'DisableCustomerController',
                            'action' => 'invalid-drivers-license'
                        ]
                    ]
                ],
                'disable-old-discounts' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'disable old discounts [--dry-run|-d] [--no-email|-e]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'DisableOldDiscountsController',
                            'action' => 'disable-old-discounts'
                        ]
                    ]
                ],
                'notify-disable-discount' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'notify disable discount [--no-email|-e]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'DisableOldDiscountsController',
                            'action' => 'notify-disable-discount'
                        ]
                    ]
                ]
            ],
        ],
    ],
];
