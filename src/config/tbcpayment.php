<?php

return [

    /**
     * This value decides to log or not to log requests.
     */
    'debug' => env('TBC_PAYMENT_DEBUG', false),

    /**
     * Payment url provided by bank
     */
    'url' => env('TBC_PAYMENT_URL', 'https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler'),

    /**
     * Certificate path in storage folder
     */
    'cert_path' => env('TBC_PAYMENT_CERT_PATH', 'app/tbc.pem'),

    /**
     * TBC certificate password
     */
    'password' => env('TBC_PAYMENT_CERT_PASSWORD'),

    /**
     * Currency for tbc payment
     */
    'currency' => env('TBC_PAYMENT_CURRENCY', 981),

    /**
     * Default language for tbc payment
     */
    'language' => env('TBC_PAYMENT_LANGUAGE', 'EN'),
];
