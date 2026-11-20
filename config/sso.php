<?php

$resolveKeyPath = static function (string $path): string {
    return str_starts_with($path, '/') ? $path : base_path($path);
};

return [
    'nm' => [
        'callback' => env('NM_SSO_CALLBACK', 'https://nm.cipfpbatoi.es/login'),
        'issuer' => env('NM_SSO_ISSUER', 'https://intranet.cipfpbatoi.es'),
        'audience' => env('NM_SSO_AUDIENCE', 'nm.cipfpbatoi.es'),
        'ttl' => (int) env('NM_SSO_TTL', 120),
        'key_id' => env('NM_SSO_KEY_ID', 'nm-2026-01'),
        'token_type' => 'nm-login+jwt',
        'private_key' => $resolveKeyPath((string) env(
            'NM_SSO_PRIVATE_KEY',
            storage_path('keys/nm-sso-private.pem')
        )),
        'public_key' => $resolveKeyPath((string) env(
            'NM_SSO_PUBLIC_KEY',
            storage_path('keys/nm-sso-public.pem')
        )),
        'passphrase' => env('NM_SSO_PASSPHRASE', ''),
    ],
];
