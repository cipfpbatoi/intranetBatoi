<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marca ús legacy de `api_token` en query/body per facilitar retirada gradual.
 */
class LegacyApiTokenDeprecation
{
    /**
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $legacyToken = $request->query('api_token') ?? $request->input('api_token');

        /** @var Response $response */
        $response = $next($request);

        if ($legacyToken !== null && $legacyToken !== '') {
            $response->headers->set('Deprecation', 'true');
            $response->headers->set('Sunset', 'Wed, 31 Dec 2026 23:59:59 GMT');
            $response->headers->set('X-API-Replacement', 'Use Authorization: Bearer <sanctum_token>');

            Log::warning('Legacy api_token usage detected', [
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}
