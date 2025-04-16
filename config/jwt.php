<?php
return [
    'private_key' => env('JWT_PRIVATE_KEY', storage_path('keys/private.pem')),
    'public_key' => env('JWT_PUBLIC_KEY', storage_path('keys/public.pem')),
    'passphrase' => env('JWT_PASSPHRASE', ''),
    'expiry' => env('JWT_EXPIRY', 3600), // 1 hora
];
