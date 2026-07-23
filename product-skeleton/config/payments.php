<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default gateway
    |--------------------------------------------------------------------------
    |
    | "thawani" talks to the real Thawani API. "fake" simulates a checkout page
    | locally so you can build and test the whole flow without API keys.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'fake'),

    'currency' => env('PAYMENT_CURRENCY', 'OMR'),

    /*
    | Thawani rejects very small amounts. Value is in baisa (100 = 0.100 OMR).
    */
    'minimum_amount' => (int) env('PAYMENT_MINIMUM_AMOUNT', 100),

    /*
    |--------------------------------------------------------------------------
    | Thawani
    |--------------------------------------------------------------------------
    |
    | Get your keys from the Thawani merchant portal:
    |   UAT (testing) : https://uatmerchant.thawani.om
    |   Live          : https://merchant.thawani.om
    |
    | secret_key      -> sent as the `thawani-api-key` header (server side only)
    | publishable_key -> appended to the hosted checkout URL (public)
    |
    */

    'thawani' => [

        // "test" uses the UAT sandbox, "live" uses production.
        'mode' => env('THAWANI_MODE', 'test'),

        'secret_key' => env('THAWANI_SECRET_KEY'),
        'publishable_key' => env('THAWANI_PUBLISHABLE_KEY'),

        // Optional shared secret you configure on the Thawani webhook.
        'webhook_secret' => env('THAWANI_WEBHOOK_SECRET'),

        'endpoints' => [
            'test' => [
                'api' => 'https://uatcheckout.thawani.om/api/v1',
                'checkout' => 'https://uatcheckout.thawani.om',
            ],
            'live' => [
                'api' => 'https://checkout.thawani.om/api/v1',
                'checkout' => 'https://checkout.thawani.om',
            ],
        ],

        'timeout' => (int) env('THAWANI_TIMEOUT', 30),
    ],

];
