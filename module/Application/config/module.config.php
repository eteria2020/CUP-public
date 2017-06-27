<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$translator = new \Zend\I18n\Translator\Translator();
// Getting the siteroot path ( = sharengo-admin folder)
$baseDir = realpath(__DIR__.'/../../../');

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
            'zoneosm' => [
                'type'    => 'Literal',
                'options' => [
                    'route' => '/zoneosm',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'get-list-osm-zones',
                    ],
                ],
            ],
            'cars' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/cars/:fleetId',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'get-list-cars-by-fleet',
                    ],
                    'constraints' => [
                        'fleetId' => '[0-9]+'
                    ],
                ],
            ],
            'pois' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/pois/:fleetId',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'get-list-pois-by-fleet',
                    ],
                    'constraints' => [
                        'fleetId' => '[0-9]+'
                    ],
                ],
            ],
            /*'carsharing' => [
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
            ],*/
            'map' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{map}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'map',
                    ],
                ]
            ],
            'map2' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{map2}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'map2',
                    ],
                ]
            ],
            /*'quantocosta' => [
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
            ],*/
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
            /*'contatti' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{contatti}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'contatti',
                    ],
                ]
            ],*/
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
            'login2' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{login2}',
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
                    'route' => '/{signup}[/:mobile]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action'     => 'signup',
                    ],
                    'constraints' => [
                        'mobile' => '[mobile]+'
                    ],
                ]
            ],
            'signup1' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup1}',
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
                    'route' => '/{signup-2}[/:mobile]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action' => 'signup2'
                    ],
                    'constraints' => [
                        'mobile' => '[mobile]+'
                    ],
                ]
            ],
            'signup-3' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{signup-3}[/:mobile]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'User',
                        'action' => 'signup3'
                    ],
                    'constraints' => [
                        'mobile' => '[mobile]+'
                    ],
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
            'agoal' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{agoal}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'agoal',
                    ],
                ]
            ],
            'aidia' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{aidia}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'aidia',
                    ],
                ]
            ],
            'alcons' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{altroconsumo}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'alcons',
                    ],
                ]
            ],
            'aldai' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{aldai}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'aldai',
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
            'arci' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{arci}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'arci',
                    ],
                ],
            ],
            'assocral' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{assocral}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'assocral',
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
            'chigi' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{chigi}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'chigi',
                    ],
                ],
            ],
            'coa' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{coa}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'coa',
                    ],
                ],
            ], 
            'coin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{coin}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'coin',
                    ],
                ],
            ], 
            'comunedifirenze' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{comunedifirenze}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'comunedifirenze',
                    ],
                ],
            ], 
            'controradioclub' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{controradioclub}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'controradioclub',
                    ],
                ],
            ],            
            'espresso' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{espresso}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'espresso',
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
            'flcg' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{falacosagiusta}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'flcg',
                    ],
                ]
            ],
            'futura' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{futura}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'futura',
                    ],
                ]
            ],
            'green' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{green}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'green',
                    ],
                ],
            ],
            'greenfi' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{greenfi}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'greenfi',
                    ],
                ],
            ],
            'gym17' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{gym17}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'gym17',
                    ],
                ],
            ],
            'intern' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{intern}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'intern',
                    ],
                ],
            ],
            'kpmg' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{kpmg}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'kpmg',
                    ],
                ],
            ],
            'la7' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{la7}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'la7',
                    ],
                ],
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
            'lumsa' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{lumsa}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'lumsa',
                    ],
                ]
            ],
            'madama' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{madama}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'madama',
                    ],
                ]
            ],
            'market' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{market}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'market',
                    ],
                ]
            ],
            'maxxi' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{maxxi}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'maxxi',
                    ],
                ]
            ],
            'ording' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{ording}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'ording',
                    ],
                ],
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
            'payback' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{payback}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'payback',
                    ],
                ],
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
            'scoac1' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{scontato}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'scoac1',
                    ],
                ]
            ],
            // disable 2017-02-13
            /*'sim1' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{sim1}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'sim1',
                    ],
                ]
            ],*/
            'svolta' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{svolta}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'svolta',
                    ],
                ]
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
            'tevere' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{tevere}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'tevere',
                    ],
                ]
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
            'vipzip' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/{vip2zip}',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'LandingPage',
                        'action'     => 'vipzip',
                    ],
                ],
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
//                    'gift-packages' => [
//                        'type' => 'Segment',
//                        'options' => [
//                            'route' => '/{gift-packages}',
//                            'defaults' => [
//                                'controller' => 'AdditionalServices',
//                                'action' => 'gift-packages'
//                            ]
//                        ]
//                    ],
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
                    'package-my-sharengo' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/package-my-sharengo',
                            'defaults' => [
                                'action' => 'package-my-sharengo'
                            ]
                        ]
                    ],
                    'payment-securecode-cartasi' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/payment-securecode-cartasi',
                            'defaults' => [
                                'action' => 'payment-securecode-cartasi'
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
                    'debt-collection' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{debt-collection}',
                            'defaults' => [
                                'action' => 'debt-collection'
                            ]
                        ]
                    ],
                    'debt-collection-payment' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/{debt-collection-payment}',
                            'defaults' => [
                                'action' => 'debt-collection-payment'
                            ]
                        ]
                    ],
                    'disable-contract' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/disable-contract',
                            'defaults' => [
                                'action' => 'disable-contract'
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
            'navigation'               => 'Zend\Navigation\Service\DefaultNavigationFactory',
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
            'Application\Controller\ConsolePromoCodesOnceCompute' => 'Application\Controller\ConsolePromoCodesOnceComputeFactory',
            'Application\Controller\ConsoleBonusCompute' => 'Application\Controller\ConsoleBonusComputeControllerFactory',
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
            'Application\Controller\ImportDriversLicenseValidations' => 'Application\Controller\ImportDriversLicenseValidationsControllerFactory',
            'Application\Controller\BirthdayBonus' => 'Application\Controller\BirthdayBonusControllerFactory',
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
            'availableFleets' => 'Application\View\Helper\AvailableFleetsFactory',
            'intercomSettings' => 'Application\View\Helper\IntercomSettingsFactory'
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
            'layout/layout'           => __DIR__ . '/../view/layout/layout2.phtml',
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
                ['controller' => 'Application\Controller\ConsolePromoCodesOnceCompute', 'roles' => []],
                ['controller' => 'Application\Controller\ConsoleBonusCompute', 'roles' => []],
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
                ['controller' => 'Application\Controller\ImportDriversLicenseValidations', 'roles' => []],
                ['controller' => 'Application\Controller\BirthdayBonus', 'roles' => []],
            ],
        ],
    ],

    'asset_manager' => [
        'caching' => [
            'default' => [
                'cache'     => 'Assetic\\Cache\\FilesystemCache',
                'options' => [
                    'dir' => $baseDir.'/data/cache',
                ],
            ],
        ],
        'resolver_configs' => [
            'collections' => [
                // JavaScript
                'assets-modules/js/vendor.map.js' => [
                    // Libs
                    'bower/ol3/ol.js',
                    'bower/ol3-ext/style/fontsymbol.js',
                    'bower/ol3-ext/style/fontawesome.def.js',
                    'bower/ol3-ext/style/shadowstyle.js',
                    'bower/ol3-geocoder/build/ol3-geocoder.js',
                    'bower/bootstrap/js/tooltip.js',
                    'bower/bootstrap/js/popover.js'
                ],
                'assets-modules/js/vendor.index.js' => [
                    // Libs
                    'assets-modules/js/vendor.map.js',
                    // Code
                    'public/js/overlay.js',
                    'public/js/perfect-scrollbar.jquery.min.js',
                    'public/js/index.map.js'
                ],
                // CSS
                'assets-modules/css/vendor.map.css' => [
                    // Libs
                    'bower/ol3/ol.css',
                    'bower/ol3-geocoder/build/ol3-geocoder.css',
                ],
                'assets-modules/css/vendor.index.css' => [
                    // Libs
                    'assets-modules/css/vendor.map.css',
                    // Code
                    'public/css/overlay.css',
                    'public/css/find-address.css',
                    'public/css/index.map.css'
                ],
            ],
            'aliases' => [
                // Bower Assets
                'bower' => $baseDir.'/bower_components',

                // Public Assets
                'public' => $baseDir.'/public',

                // Overlay Assets
                'assets-modules/img/overlay' => $baseDir.'/public/images/overlay',
                'assets-modules/images/overlay' => $baseDir.'/public/images/overlay'
            ],
            'paths' => [
                __DIR__ . '/../public',
                $baseDir.'/public',
            ]
        ],
        'filters' => [
            // Minify All JS
            'js' => [
                [
                    'filter' => 'JSMin',
                ],
            ],
            // Minify All CSS
            'css' => [
                [
                    'filter' => 'CssMin',
                ],
            ],
        ],
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
                'retry-wrong-payments' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'retry wrong payments [--no-emails|-e] [--no-cartasi|-c] [--no-db|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsolePayInvoice',
                            'action' => 'retry-wrong-payments'
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
                'promocodesonce' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'promocodesonce <actionType> <param1> <param2>',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsolePromoCodesOnceCompute',
                            'action' => 'promocode-once-main'
			]
	 	    ]
		],
                'bonus-compute' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'bonus compute [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleBonusCompute',
                            'action' => 'bonus-compute'
                        ]
                    ]
                ],
                'bonus-park' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'bonus park <data-run> <radius> <carplate> [--dry-run|-d] [--debug-mode|-dm]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ConsoleBonusCompute',
                            'action' => 'bonus-pois'
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
                'disable-customers-expired-license' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'disable customers expired license [--debug-mode|-dm]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'DisableCustomerController',
                            'action' => 'expired-drivers-license'
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
                ],
                'test-license-validation' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'test license validation [--dry-run|-d] [--use-data] [--id=] [--email=] [--valid=] [--code=] [--msg=]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ImportDriversLicenseValidations',
                            'action' => 'test-validation'
                        ]
                    ]
                ],
                'import-license-validations' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'import license validations [--dry-run|-d] [--verbose|-v] [--one|-o]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'ImportDriversLicenseValidations',
                            'action' => 'import-validations'
                        ]
                    ]
                ],
                'assign birthday bonuses' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'assign birthday bonuses [--dry-run|-d]',
                        'defaults' => [
                            '__NAMESPACE__' => 'Application\Controller',
                            'controller' => 'BirthdayBonus',
                            'action' => 'assign-birthday-bonuses'
                        ]
                    ]
                ]
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => $translator->translate("Profilo"),
                'route' => 'area-utente',
                'icon' => 'fa fa-user',
            ],
            [
                'label' => $translator->translate("Patente"),
                'route' => 'area-utente/patente',
                'icon' => 'fa fa-tachometer',
            ],
            [
                'label' => $translator->translate("PIN"),
                'route' => 'area-utente/pin',
                'icon' => 'fa fa-lock',
            ],
            [
                'label' => $translator->translate("Tariffe"),
                'route' => 'area-utente/tariffe',
                'icon' => 'fa fa-money',
            ],
            [
                'label' => $translator->translate("Promo e pacchetti"),
                'route' => 'area-utente/additional-services',
                'icon' => 'fa fa-plus',
            ],
//            [
//                'label' => $translator->translate("Gift Card"),
//                'route' => 'area-utente/gift-packages',
//                'icon' => 'fa fa-gift',
//            ],
            [
                'label' => $translator->translate("Bonus minuti"),
                'route' => 'area-utente/bonus',
                'icon' => 'fa fa-trophy',
            ],
            [
                'label' => $translator->translate("Corse completate"),
                'route' => 'area-utente/rents',
                'icon' => 'fa icon-car-small-fff',
            ],
            [
                'label' => $translator->translate("Dati di pagamento"),
                'route' => 'area-utente/dati-pagamento',
                'icon' => 'fa fa-credit-card',
            ],
            [
                'label' => $translator->translate("Fatture"),
                'route' => 'area-utente/invoices-list',
                'icon' => 'fa fa-file-o',
            ],
        ],
    ],
];
