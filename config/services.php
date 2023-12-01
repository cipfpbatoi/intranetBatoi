<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => Intranet\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_ID', '722193940751-ae76pbqg2fr7rb95r6bh139sdc6cl8a8.apps.googleusercontent.com'),
        'client_secret' => env('GOOGLE_SECRET', 'QU20APPRUWBlDlp839bI3P98'),
        'redirect' => env('GOOGLE_REDIRECT', config('contacto.host.web').'/social/callback/google'),
    ],
    'google-calendar' => [
        'client_id' => env('GOOGLE_ID', '24143841601-1per4o1b4sheviki8a42uqtns2qma7ku.apps.googleusercontent.com'),
        'client_secret' => env('GOOGLE_SECRET', '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'),
        'redirect' => env('GOOGLE_REDIRECT', config('contacto.host.web').'/social/calendar'),
    ],
    'selenium' => [
        'url' => env('SELENIUM_URL', '172.16.9.10:4444'),
        'SAO' => env('SELENIUM_URL_SAO', 'https://foremp.edu.gva.es/index.php'),
        'SAO_USER' => env('SELENIUM_USER_SAO', '21668389C'),
        'SAO_PASS' => env('SELENIUM_PASS_SAO', '21668389C'),
        'SELENIUM_ROOT_PASS' => env('SELENIUM_ROOT_PASS', 'intranet'),
        'itaca' => env('SELENIUM_URL_ITACA','https://acces.edu.gva.es/sso/login.xhtml?callbackUrl=https://acces.edu.gva.es/escriptori/'),
    ]

];
