<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Intranet\Services\Auth\RemoteIntranetTokenService;
use RuntimeException;
use Throwable;

/**
 * Importa dades des d'una intranet remota autenticada amb Sanctum.
 */
class RemoteIntranetImportService
{
    private const ALUMNO_FCT_COLUMNS = [
        'idFct',
        'idAlumno',
        'calificacion',
        'calProyecto',
        'actas',
        'insercion',
        'horas',
        'valoracio',
        'desde',
        'hasta',
        'correoAlumno',
        'pg0301',
        'beca',
        'a56',
        'idSao',
        'realizadas',
        'horas_diarias',
        'actualizacion',
        'autorizacion',
        'flexible',
        'idProfesor',
    ];

    public function __construct(
        private readonly RemoteIntranetTokenService $tokens,
        private readonly HttpFactory $http
    ) {
    }

    /**
     * Importa `alumno_fcts` des de l'endpoint remot `/alumnofct`.
     *
     * @return array{created:int, updated:int, skipped:int, errors:int}
     */
    public function importAlumnoFcts(): array
    {
        $token = $this->tokens->bearerToken();
        $response = $this->http
            ->timeout($this->tokens->timeout())
            ->acceptJson()
            ->withToken($token)
            ->get($this->tokens->baseUrl() . '/alumnofct');

        if (!$response->successful()) {
            throw new RuntimeException('No s\'han pogut obtenir les FCT remotes.');
        }

        $items = $this->extractItems($response->json());
        $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];

        DB::transaction(function () use ($items, &$summary): void {
            foreach ($items as $item) {
                if (!is_array($item)) {
                    $summary['skipped']++;
                    continue;
                }

                try {
                    $result = $this->upsertAlumnoFct($item);
                    $summary[$result]++;
                } catch (Throwable) {
                    $summary['errors']++;
                }
            }
        });

        return $summary;
    }

    /**
     * Normalitza els formats habituals de resposta JSON.
     *
     * @return array<int, mixed>
     */
    private function extractItems(mixed $payload): array
    {
        if (is_array($payload) && array_is_list($payload)) {
            return $payload;
        }

        foreach (['data.data', 'data', 'items'] as $key) {
            $items = Arr::get($payload, $key);
            if (is_array($items) && array_is_list($items)) {
                return $items;
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $item
     */
    private function upsertAlumnoFct(array $item): string
    {
        $payload = $this->payloadForAlumnoFct($item);
        if (!isset($payload['idFct'], $payload['idAlumno'])) {
            return 'skipped';
        }

        $query = DB::table('alumno_fcts');
        if (!empty($payload['idSao'])) {
            $query->where('idSao', $payload['idSao']);
        } else {
            $query
                ->where('idFct', $payload['idFct'])
                ->where('idAlumno', $payload['idAlumno']);
        }

        $existing = $query->first();
        if ($existing) {
            DB::table('alumno_fcts')->where('id', $existing->id)->update($payload);
            return 'updated';
        }

        DB::table('alumno_fcts')->insert($payload);
        return 'created';
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function payloadForAlumnoFct(array $item): array
    {
        $payload = [];
        foreach (self::ALUMNO_FCT_COLUMNS as $column) {
            if (array_key_exists($column, $item)) {
                $payload[$column] = $item[$column];
            }
        }

        return $payload;
    }
}
