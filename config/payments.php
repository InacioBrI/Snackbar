<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Which gateway implementation to use. "mock" simulates approvals locally
    | (great for development and demos). Swap to "mercadopago" once real
    | credentials are provided.
    |
    */

    'driver' => env('PAYMENT_DRIVER', 'mock'),

    /*
    |--------------------------------------------------------------------------
    | PIX / Merchant Data
    |--------------------------------------------------------------------------
    */

    'pix' => [
        'key' => env('PIX_KEY', '00000000000'),
        'merchant_name' => env('PIX_MERCHANT_NAME', 'LANCHONETE SHOPPING'),
        'merchant_city' => env('PIX_MERCHANT_CITY', 'SAO PAULO'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mercado Pago (structure ready, plug credentials to enable)
    |--------------------------------------------------------------------------
    */

    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    ],

];
