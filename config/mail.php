<?php

$defaultMailer = env('MAIL_MAILER', env('MAIL_DRIVER', 'smtp'));
$smtpHost = env('MAIL_HOST', '127.0.0.1');
$smtpPort = (int) env('MAIL_PORT', 25);
$smtpEncryption = env('MAIL_ENCRYPTION');
$smtpUsername = env('MAIL_USERNAME');
$smtpPassword = env('MAIL_PASSWORD');

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | Laravel 12 utilitza "MAIL_MAILER", però mantenim compatibilitat amb
    | "MAIL_DRIVER" per als entorns antics del projecte i proves existents.
    |
    */

    'default' => $defaultMailer,

    /*
    |--------------------------------------------------------------------------
    | Legacy Driver Key
    |--------------------------------------------------------------------------
    |
    | Alguns punts del projecte encara poden llegir "mail.driver". Es manté
    | sincronitzada amb el mailer per evitar regressions durant la transició.
    |
    */

    'driver' => $defaultMailer,

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => $smtpHost,
            'port' => $smtpPort,
            'encryption' => $smtpEncryption ?: null,
            'username' => $smtpUsername ?: null,
            'password' => $smtpPassword ?: null,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp', 'log'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy SMTP Keys
    |--------------------------------------------------------------------------
    |
    | Es mantenen per compatibilitat amb codi històric que puga consultar-los.
    |
    */

    'host' => $smtpHost,
    'port' => $smtpPort,
    'encryption' => $smtpEncryption ?: null,
    'username' => $smtpUsername ?: null,
    'password' => $smtpPassword ?: null,

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', config('contacto.host.email')),
        'name' => env('MAIL_FROM_NAME', 'Intranet'),
    ],
];
