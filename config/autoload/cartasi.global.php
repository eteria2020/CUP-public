<?php

return [
    'cartasi' => [
        'first_payment_amount' => 500,
        'preauthorization_amount' => 5,
        'currency' => 'EUR',
        'currency_ecreq' => 978,
        'first_payment_url' => 'https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet',
        'first_payment_description' => '',
        'recurring_payment_url' => 'https://ecommerce.nexi.it/ecomm/ecomm/ServletS2S',
        'recurring_payment_description' => '',
        'refunding_payment_url' => 'https://ecommerce.nexi.it/ecomm/ecomm/XPayBo',
        'number_of_retries' => 1,
        'pause_between_retries' => 1,
        'valid_ip' => '127.0.0.1;185.58.119.117'
    ]
];
