<?php

namespace Intranet\Http\Controllers\API;

use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Profesor;

/**
 * Gestió de tokens d'accés API en fase de coexistència legacy + Sanctum.
 */
class AuthTokenController extends ApiResourceController
{
    private ?ProfesorService $profesorService = null;

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    /**
     * Intercanvia `api_token` legacy per un token Sanctum.
     */
    public function exchange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'api_token' => 'required|string|min:8',
            'device_name' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:12',
        ]);

        $profesor = $this->profesores()->findByApiToken((string) $validated['api_token']);
        if (!$profesor) {
            return $this->sendError('Unauthorized', 401);
        }

        if (isset($validated['dni']) && (string) $validated['dni'] !== (string) $profesor->dni) {
            return $this->sendError('Unauthorized', 401);
        }

        $deviceName = trim((string) ($validated['device_name'] ?? 'legacy-exchange'));
        $expiration = config('sanctum.expiration');
        $expiresAt = is_numeric($expiration)
            ? (new DateTimeImmutable())->modify('+'.((int) $expiration).' minutes')
            : null;

        $token = $profesor->createToken($deviceName, ['*'], $expiresAt);

        return response()->json([
            'success' => true,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
                'dni' => $profesor->dni,
            ],
        ]);
    }

    /**
     * Retorna usuari API autenticat (compat: `auth:api` o `auth:sanctum`).
     */
    public function me(Request $request): JsonResponse
    {
        /** @var Profesor|null $user */
        $user = $request->user();
        if (!$user) {
            return $this->sendError('Unauthorized', 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'dni' => $user->dni,
                'nombre' => $user->nombre,
                'apellido1' => $user->apellido1,
                'apellido2' => $user->apellido2,
                'rol' => $user->rol,
            ],
        ]);
    }

    /**
     * Revoca el token actual quan la petició entra amb Sanctum.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var Profesor|null $user */
        $user = $request->user();
        if (!$user) {
            return $this->sendError('Unauthorized', 401);
        }

        $currentToken = $user->currentAccessToken();
        if ($currentToken !== null) {
            $currentToken->delete();
        }

        return response()->json([
            'success' => true,
            'data' => ['revoked' => true],
        ]);
    }
}
