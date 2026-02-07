<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET')
    ],

    // 'stripe' => [
    //     'nexus' => [
    //         'key' => env('STRIPE_KEY_NEXUS'),
    //         'secret' => env('STRIPE_SECRET_NEXUS'),
    //     ],
    //     'devxperts' => [
    //         'key' => env('STRIPE_KEY_DEVXPERTS'),
    //         'secret' => env('STRIPE_SECRET_DEVXPERTS'),
    //     ],
    // ],

    'paypal' => [
        // sandbox: https://api-m.sandbox.paypal.com
        // live:    https://api-m.paypal.com
        'base'       => env('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com'),
        'client_id'  => env('PAYPAL_CLIENT_ID'),
        'secret'     => env('PAYPAL_SECRET'),
        // optional but recommended for signature verification
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],


    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
        'key'    => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET'),
    ],

];
