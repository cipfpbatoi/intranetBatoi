<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers\Auth\Sso;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Requests\Auth\Sso\NmSsoRequest;
use Intranet\Services\Auth\NmSsoTokenService;
use LogicException;

/**
 * Completa el SSO de professorat amb un POST signat cap a NM.
 */
class NmSsoController extends Controller
{
    /**
     * Emet el JWT i renderitza el formulari de retorn fix.
     */
    public function __invoke(NmSsoRequest $request, NmSsoTokenService $tokens): Response
    {
        $profesor = Auth::guard('profesor')->user();
        if (!$profesor instanceof Profesor) {
            abort(403);
        }

        $callback = $this->callback();
        $nonce = base64_encode(random_bytes(18));
        $response = response()->view('auth.sso.post', [
            'callback' => $callback,
            'nonce' => $nonce,
            'state' => (string) $request->validated('state'),
            'token' => $tokens->issueFor($profesor),
        ]);

        return $response
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'DENY')
            ->header(
                'Content-Security-Policy',
                "default-src 'none'; script-src 'nonce-{$nonce}'; form-action {$callback}; "
                . "base-uri 'none'; frame-ancestors 'none'"
            );
    }

    /**
     * Retorna l'única URL de retorn admesa pel contracte.
     */
    private function callback(): string
    {
        $callback = trim((string) config('sso.nm.callback'));
        $parts = parse_url($callback);
        if (
            $callback === ''
            || !is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || !isset($parts['host'])
        ) {
            throw new LogicException('La URL de retorn del SSO de NM no és vàlida.');
        }

        return $callback;
    }
}
