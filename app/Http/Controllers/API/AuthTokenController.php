<?php

namespace Intranet\Http\Controllers\API;

use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Profesor;
use OpenApi\Attributes as OA;

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
    #[OA\Post(
        path: '/auth/exchange',
        operationId: 'authExchange',
        summary: 'Intercanvi de token legacy a token Sanctum',
        tags: ['Auth (Public)'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['api_token'],
                properties: [
                    new OA\Property(property: 'api_token', type: 'string', example: 'legacy-token-xyz'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'mobile-app'),
                    new OA\Property(property: 'dni', type: 'string', example: '12345678A'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token intercanviat correctament',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(property: 'access_token', type: 'string', example: '1|abc123...'),
                                new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
                                new OA\Property(property: 'dni', type: 'string', example: '12345678A'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autoritzat',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiError')
            ),
        ]
    )]
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
     * Retorna usuari API autenticat amb Sanctum (Bearer).
     */
    #[OA\Get(
        path: '/auth/me',
        operationId: 'authMe',
        summary: 'Retorna l usuari autenticat',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuari autenticat',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'dni', type: 'string', example: '12345678A'),
                                new OA\Property(property: 'nombre', type: 'string', example: 'Maria'),
                                new OA\Property(property: 'apellido1', type: 'string', example: 'Garcia'),
                                new OA\Property(property: 'apellido2', type: 'string', nullable: true, example: 'Perez'),
                                new OA\Property(property: 'rol', type: 'string', example: 'professor'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autoritzat',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiError')
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/auth/logout',
        operationId: 'authLogout',
        summary: 'Revoca el token actual',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token revocat',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'revoked', type: 'boolean', example: true),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autoritzat',
                content: new OA\JsonContent(ref: '#/components/schemas/ApiError')
            ),
        ]
    )]
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
