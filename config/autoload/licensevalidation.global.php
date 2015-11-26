<?php

return [
    'drivers-license-validation' => [
        'portale-automobilista' => [
            'url' => 'https://www.ilportaledellautomobilista.it/Info-ws/services/verificaValiditaPatente/verificaValiditaPatente.wsdl',
            'username' => 'PRLI000101',
            'password' => 'SHARE13!',
            'wsse-namespace' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
            'connection_timeout' => 10
        ]
    ]
];
