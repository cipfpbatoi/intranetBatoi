<?php

declare(strict_types=1);

namespace Intranet\Services\Auth;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Arr;
use RuntimeException;

/**
 * Gestiona l'autenticació contra una intranet remota amb pont legacy a Sanctum.
 */
class RemoteIntranetTokenService
{
    public function __construct(private readonly HttpFactory $http)
    {
    }

    /**
     * Intercanvia el token legacy configurat per un Bearer Sanctum remot.
     *
     * @throws RuntimeException
     */
    public function bearerToken(): string
    {
        $baseUrl = $this->baseUrl();
        $legacyToken = (string) config('services.remote_intranet.api_token', '');

        if ($legacyToken === '') {
            throw new RuntimeException('Falta configurar el token remot.');
        }

        $response = $this->http
            ->timeout($this->timeout())
            ->acceptJson()
            ->asJson()
            ->post($baseUrl . '/auth/exchange', [
                'api_token' => $legacyToken,
                'device_name' => (string) config('services.remote_intranet.device_name', 'intranet-import'),
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('No s\'ha pogut autenticar amb la intranet remota.');
        }

        $token = Arr::get($response->json(), 'data.access_token');
        if (!is_string($token) || trim($token) === '') {
            throw new RuntimeException('La intranet remota no ha retornat cap token Bearer.');
        }

        return $token;
    }

    /**
     * Retorna la URL base de l'API remota sense barra final.
     *
     * @throws RuntimeException
     */
    public function baseUrl(): string
    {
        $baseUrl = rtrim((string) config('services.remote_intranet.url', ''), '/');
        if ($baseUrl === '') {
            throw new RuntimeException('Falta configurar la URL de la intranet remota.');
        }

        return $baseUrl;
    }

    /**
     * Retorna el timeout HTTP configurat.
     */
    public function timeout(): int
    {
        return max(1, (int) config('services.remote_intranet.timeout', 20));
    }
}
