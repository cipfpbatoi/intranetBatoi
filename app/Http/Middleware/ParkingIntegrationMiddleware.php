<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protegeix els endpoints d'integració de la porta amb un token compartit.
 *
 * Els webhooks de càmera poden requerir a més una allowlist d'IP/CIDR.
 */
class ParkingIntegrationMiddleware
{
    /**
     * Valida el token d'integració rebut per header abans d'accedir al webhook.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ?string $mode = null): Response
    {
        $expectedToken = (string) config('variables.domotica.integration_token', '');

        if ($expectedToken === '') {
            return response()->json(['error' => 'Parking integration not configured'], 503);
        }

        $providedToken = (string) ($request->header('X-Parking-Token')
            ?? $request->header('X-Integration-Token')
            ?? '');

        if (!hash_equals($expectedToken, $providedToken)) {
            return response()->json(['error' => 'Unauthorized integration request'], 401);
        }

        if ($mode === 'ip' && !$this->ipIsAllowed((string) $request->ip())) {
            return response()->json(['error' => 'Unauthorized integration source'], 403);
        }

        return $next($request);
    }

    /**
     * Comprova si la IP d'origen està dins de l'allowlist configurat.
     */
    private function ipIsAllowed(string $ip): bool
    {
        $allowlist = config('variables.domotica.integration_allowlist', []);

        if (is_string($allowlist)) {
            $allowlist = array_filter(array_map('trim', explode(',', $allowlist)));
        }

        if (!is_array($allowlist) || $allowlist === []) {
            return false;
        }

        foreach ($allowlist as $allowedRange) {
            if ($this->matchesAllowedRange($ip, (string) $allowedRange)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resol coincidència exacta o per CIDR IPv4 simple.
     */
    private function matchesAllowedRange(string $ip, string $allowedRange): bool
    {
        if ($allowedRange === '') {
            return false;
        }

        if (str_contains($allowedRange, '/')) {
            [$subnet, $mask] = array_pad(explode('/', $allowedRange, 2), 2, null);
            $mask = is_numeric($mask) ? (int) $mask : -1;

            if ($mask < 0 || $mask > 32) {
                return false;
            }

            $ipLong = ip2long($ip);
            $subnetLong = ip2long((string) $subnet);

            if ($ipLong === false || $subnetLong === false) {
                return false;
            }

            if ($mask === 0) {
                return true;
            }

            $networkMask = -1 << (32 - $mask);

            return ($ipLong & $networkMask) === ($subnetLong & $networkMask);
        }

        return $ip === $allowedRange;
    }
}
