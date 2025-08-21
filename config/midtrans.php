<?php


return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    'callbacks' => [
        'finish' => env('APP_URL') . '/payment-success/{id}',
        'notification' => env('APP_URL') . '/midtrans-callback',
        'unfinish' => env('APP_URL') . '/payment-silver',
        'error' => env('APP_URL') . '/payment-silver',
    ],
];