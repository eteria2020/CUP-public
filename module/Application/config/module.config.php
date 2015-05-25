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
            'cars' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/cars',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Cars',
                        'action'     => 'positions',
                    ]
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
            'RegistrationService' => 'Application\Service\RegistrationServiceFactory',
            'RegistrationForm' => 'Application\Form\RegistrationFormFactory',
            'RegistrationForm2' => 'Application\Form\RegistrationForm2Factory',
            'PaypalRequest' => 'Application\Service\PaypalRequestFactory',
            'ProfilingPlatformService' => 'Application\Service\ProfilingPlatformServiceFactory',
            'PaymentService' => 'Application\Service\PaymentServiceFactory',
            'ProfileForm' => 'Application\Form\ProfileFormFactory',
            'PasswordForm' => 'Application\Form\PasswordFormFactory',
        ],
        'invokables' => [
            'Application\Authentication\Adapter\Sharengo' => 'Application\Authentication\Adapter\Sharengo',
            'goalioforgotpassword_password_service' => 'Application\Service\PasswordService',
        ]
    ),
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Cars' => 'Application\Controller\CarsController'
        ],
        'factories' => [
            'Application\Controller\User' => 'Application\Controller\UserControllerFactory',
            'Application\Controller\Payment' => 'Application\Controller\PaymentControllerFactory',
            'Application\Controller\UserArea' => 'Application\Controller\UserAreaControllerFactory',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'CurrentRoute' => 'Application\View\Helper\CurrentRouteFactory',
            'LongLanguage' => 'Application\View\Helper\LongLanguageFactory',
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
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),

    // ACL
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(

                array('controller' => 'zfcuser', 'roles' => array()),
                array('controller' => 'goalioforgotpassword_forgot', 'roles' => array()),
                array('controller' => 'Application\Controller\Index', 'roles' => array()),
                array('controller' => 'Application\Controller\User', 'roles' => array()),
                array('controller' => 'Application\Controller\Cars', 'roles' => array()),
                array('controller' => 'Application\Controller\UserArea', 'roles' => array('user')),

            ),
        ),
    ),
);
