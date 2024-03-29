<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'sharengo' => [
        'card-cost' => 5
    ],
    'profiling-platform' => [
        'endpoint' => 'http://www.equomobili.it/',
        'getdiscount-call' => 'getdiscount.php?email=%s',
        'getpromocode-call' => 'getcodicesconto.php?email=%s',
        'getfleet-call' => 'getcitta.php?email=%s'
    ],
    'api' => [
        'url' => 'http://%sapi.sharengo.it:8021/v2'
    ],
    'mobile' => [
        'url' => 'http://mobile.sharengo.it'
    ],
/**    'export' => [
        'path' => 'data/export/',
        'server' => 'dev.sharengo.it',
        'name' => 'fatture',
        'password' => 'f477ur3!'
    ], */
	'export' => [
        'path' => 'invoices/',
        'server' => 'ftp.sharengo.si',
        'name' => 'invoices@sharengo.si',
        'password' => 'wrS7^q7tYd{['
    ],
    'languageSession' => [
        'session' => 'user',
        'offset' => 'lang'
    ],
    'subscription-bonus' => [
        'total' => 21,
        'description' => 'Pacchetto di Benvenuto 21 minuti',
        'valid-to' => '+ 90 days'
    ],
    'banner-jsonp' => [
        'url' => 'banner'
    ],
    'bonus' => [
        'birthday' => [
            'total' => 30,
            'description' => 'Bonus compleanno'
        ],
        'zones' => [
            'defaultTotal' => 10,
            'defaultDuration' => 30,
            'Carrefour' => [
                'fixedBonus' => 30,
                'minMinutes' => 5,
                'total' => 30,
                'duration' => 60
            ]
        ]
    ]
];
