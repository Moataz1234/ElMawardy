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
 'asgardeo' => [
    'client_id' => env('ASGARDEO_CLIENT_ID'),
    'client_secret' => env('ASGARDEO_CLIENT_SECRET'),
    'redirect' => env('ASGARDEO_REDIRECT_URI'),
    'authorize_url' => env('ASGARDEO_AUTHORIZE_URL'),
    'token_url' => env('ASGARDEO_TOKEN_URL'),
    'userinfo_url' => env('ASGARDEO_USERINFO_URL'),
    'logout_url' => env('ASGARDEO_LOGOUT_URL'),
],

    'passport' => [
    'client_id' => env('PASSPORT_CLIENT_ID'),
    'client_secret' => env('PASSPORT_CLIENT_SECRET'),
    'redirect' => env('PASSPORT_REDIRECT_URI'),
    ],
    
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

];
