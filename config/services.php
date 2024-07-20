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
    'google_place' => [
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => 'http://127.0.0.1:8000/api/social-login/callback/google',
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => 'http://127.0.0.1:8000/api/social-login/callback/facebook',
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => 'http://axiomeds.test/api/social-login/callback/github',
    ],

    'xero' => [
        'clientId'                => env('XERO_CLIENT_ID','50E9147AC48947E7B6BA1A6549C56520'),
        'clientSecret'            => env('XERO_CLIENT_SECRET','pZTNb_sKABMo_6hUJdhvkmWjndWdv3RhoOzmZD_ch0-xn1YK'),
        'redirectUri'             => env('XERO_REDIRECT_URL','https://axiomeds.test/api/setting/xeroCallback'),
        'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
        'urlAccessToken'          => 'https://identity.xero.com/connect/token',
        'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
    ],
    'zoho' => [
        'client_id'                => env('ZOHO_CLIENT_ID','1000.5B3K14KQLXO960EAVAWIIW7Y57KE9H'),
        'clientSecret'            => env('ZOHO_CLIENT_SECRET','f6c4f9ec06281311d80d25ade5828a2b1ad4ada3f7'),
        'redirect_uri'             => env('ZOHO_REDIRECT_URL','http://axiomeds.test/api/setting/zohoCallback'),
    ]

];
