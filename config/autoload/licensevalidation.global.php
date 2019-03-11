<?php

return [
    'drivers-license-validation' => [
        'portale-automobilista' => [
            'url' => 'http://license.sharengo.it:8080/check_dl.php'
        ],
        'logDir' => '/var/log/sharengo-publicsite/data/log/driversLicense.log'
    ],
    'zf2resque' => [
        'redisDatabase' => 5
    ],
];
