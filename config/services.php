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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'otp' => [
        'secret_key' => env('OTP_SECRET', '4mA4hdZFigQnHCt94'),
        'expiration_time' => 1200,
        'digits_no' => 5,
        'hash' => env('DEFAULT_HASH', 'sha1'),
    ],

    'emailable' => [
        'secretkey' => env('EMAILABLEY_SECRET_KEY'),
    ],

    'paystack' => [
        'productionSecretKey' => env('PAYSTACK_PRODUCTION_SECRET'),
        'productionPublicKey' => env('PAYSTACK_PRODUCTION_PUBLIC'),
        'stagingPublicKey' => env('PAYSTACK_STAGING_PUBLIC'),
        'stagingSecretKey' => env('PAYSTACK_STAGING_SECRET'),
        'mode' => env('PAYSTACK_MODE', 'live'),
    ],

    'rekognition' => [
        'secret_key' => env('REC_SEC'),
        'access_key' => env('REC_ACC'),
    ],

];
