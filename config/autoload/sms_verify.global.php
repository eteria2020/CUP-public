<?php

return [
    'sms' => [
        'username'  => 'SMSHY8YFB8Z1JHFFQD139',
        'password'  => 'YHFODFXUGD9IE04U1PK0PIDKZ76SVFXO',
        'url'       => "https://api.smshosting.it/rest/api/sms/send",
        'sandbox'   => 'true',
        //'sandbox' => 'false',
        'from'      => 'ShareNGO',
        'text'      => 'Codice di Verifica ',
        'logError'   => '/tmp/logErrorSms.txt',
        'logSuccess' => '/tmp/logSuccesSms.txt'
    ]
];
