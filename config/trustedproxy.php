<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    |
    | Llista de proxies de confiança usada pel middleware TrustProxies.
    | Pot ser '*' per confiar en tots els proxies o una llista separada per
    | comes amb IPs o rangs CIDR.
    |
    */
    'proxies' => env('TRUSTED_PROXIES'),
];
