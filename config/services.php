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

    'erp' => [
        'url' => env('ERP_API_URL', 'http://207.180.213.98:80/api/RunSql'),
        'connection_string' => env('ERP_CONNECTION_STRING', 'user id=sa;pwd=Ts@2008@;Data Source=5.189.161.154;database=demo_website;'),
    ],

    'shop2topup' => [
        'base_url' => env('SHOP2TOPUP_BASE_URL', 'https://shop2topup.com/api/shopapi/v1'),
        'api_key' => env('SHOP2TOPUP_API_KEY'),
        'timeout' => env('SHOP2TOPUP_TIMEOUT', 20),
    ],

];