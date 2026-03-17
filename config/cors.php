<?php

$allowedOrigins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost,https://localhost,http://127.0.0.1,https://127.0.0.1'
    ))
)));

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'signatura/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
