<?php

return [
    'gpwebpay' => [
        'first_payment_url' => 'https://3dsecure.gpwebpay.com/pgw/order.do',
        'recurring_payment_url' => 'https://3dsecure.gpwebpay.com/pay-ws/v1/PaymentService',
        'currency' => 978, //EUR
        'provider' => 7500,
        'our_private_key' => 'gpwebpay-pvk.key',
        'gpwebpay_key' => 'gpe.signing_prod.pem',
        'first_payment_amount' => 500,
        'merchant_number' => '3083090',
        'private_key_password' => 'Platbycezinternet245',
        'valid_ip' => ''
    ]
];
