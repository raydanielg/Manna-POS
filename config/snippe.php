<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Snippe Payment Gateway
    |--------------------------------------------------------------------------
    |
    | API keys and configuration for Snippe payment processing.
    | Get your keys at https://snippe.sh/dashboard
    |
    */

    'api_key' => env('SNIPPE_API_KEY', ''),

    'environment' => env('SNIPPE_ENV', 'sandbox'), // sandbox or production

    'webhook_secret' => env('SNIPPE_WEBHOOK_SECRET', ''),

    'base_url' => 'https://api.snippe.sh',

    'checkout_base' => 'https://snippe.me',
];
