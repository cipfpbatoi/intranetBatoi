<?php

namespace Intranet\Services\Auth;

use DateTimeImmutable;
use Intranet\Entities\Profesor;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Gestiona el token Sanctum de sessió web per al professorat.
 */
class ApiSessionTokenService
{
    private const SESSION_TOKEN_KEY = 'api_access_token';
    private const SESSION_TOKEN_ID_KEY = 'api_access_token_id';

    /**
     * Emet un token Sanctum i el guarda en sessió per a ús del client web.
     */
    public function issueForProfesor(Profesor $profesor, string $deviceName = 'web-session'): string
    {
        $this->revokeCurrentFromSession();

        $expiration = config('sanctum.expiration');
        $expiresAt = is_numeric($expiration)
            ? (new DateTimeImmutable())->modify('+'.((int) $expiration).' minutes')
            : null;

        $newToken = $profesor->createToken($deviceName, ['*'], $expiresAt);

        session([
            self::SESSION_TOKEN_KEY => $newToken->plainTextToken,
            self::SESSION_TOKEN_ID_KEY => $newToken->accessToken->id,
        ]);

        return $newToken->plainTextToken;
    }

    /**
     * Revoca el token actual emmagatzemat en sessió i neteja claus de sessió.
     */
    public function revokeCurrentFromSession(): void
    {
        $tokenId = session(self::SESSION_TOKEN_ID_KEY);
        if ($tokenId !== null) {
            PersonalAccessToken::query()->whereKey($tokenId)->delete();
        }

        session()->forget([
            self::SESSION_TOKEN_KEY,
            self::SESSION_TOKEN_ID_KEY,
        ]);
    }

    /**
     * Retorna el token de sessió actual, si existeix.
     */
    public function currentToken(): ?string
    {
        $token = session(self::SESSION_TOKEN_KEY);
        return is_string($token) && $token !== '' ? $token : null;
    }
}
