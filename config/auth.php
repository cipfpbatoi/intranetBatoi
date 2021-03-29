<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'profesor',
        'passwords' => 'profesores',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'profesores',
        ],
        'profesor' => [
            'driver' => 'session',
            'provider' => 'profesores',
        ],
        'alumno' => [
            'driver' => 'session',
            'provider' => 'alumnos',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'profesores',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Profesor Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'alumnos' => [
            'driver' => 'eloquent',
            'model' => Intranet\Entities\Alumno::class,
        ],
        'profesores' => [
            'driver' => 'eloquent',
            'model' => Intranet\Entities\Profesor::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'profesores' => [
            'provider' => 'profesores',
            'email' => 'auth.profesores.email.password',
            'table' => 'profesores_password_resets',
            'expire' => 60,
        ],
        'alumnos' => [
            'provider' => 'alumnos',
            'email' => 'auth.profesores.email.password',
            'table' => 'alumnos_password_resets',
            'expire' => 60,
        ],
    ],

];
