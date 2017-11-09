<?php

return [
    'cartasi' => [
        'first_payment_amount' => 1000,
        'preauthorization_amount' => 500,
        'currency' => 'EUR',
        'currency_ecreq' => 978,
        'first_payment_url' => 'https://ecommerce.cartasi.it/ecomm/ecomm/DispatcherServlet',
        'first_payment_description' => '',
        'recurring_payment_url' => 'https://ecommerce.cartasi.it/ecomm/ecomm/ServletS2S',
        'recurring_payment_description' => '',
        'refunding_payment_url' => 'https://ecommerce.cartasi.it/ecomm/ecomm/XPayBo',
        'number_of_retries' => 3,
        'pause_between_retries' => 1,
        'valid_ip' => '127.0.0.1;185.58.119.117'
    ]
];
