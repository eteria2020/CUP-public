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
        'card-cost' => 10
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
    'export' => [
        'path' => 'data/export/',
        'server' => 'dev.sharengo.it',
        'name' => 'fatture',
        'password' => 'f477ur3!'
    ],
    'subscription-bonus' => [
        'total' => 30,
        'description' => 'Bonus iscrizione utente',
        'valid-to' => '+ 90 days'
    ],
    'banner-jsonp' => [
        'url' => 'http://manage.sharengo.it/banner.php'
    ],
];
