<?php

namespace Intranet\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Proxies de confiança llegits des de la variable d'entorn TRUSTED_PROXIES.
     * Exemples: '*' (preproducció), '10.0.0.1' o '10.0.0.0/8' (producció).
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Inicialitza els proxies de confiança des de la variable d'entorn TRUSTED_PROXIES.
     * Permet configurar-los per entorn sense canviar el codi.
     */
    public function __construct()
    {
        $trusted = config('trustedproxy.proxies');
        if ($trusted === '*') {
            $this->proxies = '*';
        } elseif ($trusted) {
            $this->proxies = array_map('trim', explode(',', $trusted));
        }
    }

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
