<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Support\Facades\DB;
use Intranet\Services\UI\AppAlert as Alert;

class TeacherImportExecutionService
{
    private int $plantilla = 0;

    public function __construct(
        private readonly HorarioService $horarioService,
        private readonly ProfesorService $profesorService,
    ) {
    }

    public function clearTeacherHorarios(string $idProfesor, bool $lost = false): void
    {
        if (!$lost) {
            $this->plantilla = (int) (DB::table('horarios')->orderBy('plantilla', 'desc')->value('plantilla') ?? 0);
        } else {
            $this->plantilla = (int) (DB::table('horarios')
                ->where('idProfesor', $idProfesor)
                ->orderBy('plantilla', 'desc')
                ->value('plantilla') ?? 0);
        }

        DB::table('horarios')->where('idProfesor', $idProfesor)->delete();
    }

    /**
     * @param array<string, mixed> $tabla
     * @param callable(mixed, mixed, int): mixed $extractField
     * @param callable(array<int, string>, mixed): bool $passesFilter
     * @param callable(array<int, string>, mixed): bool $requiredCheck
     */
    public function importTable(
        mixed $xmltable,
        array $tabla,
        string $idProfesor,
        callable $extractField,
        callable $passesFilter,
        callable $requiredCheck,
        string $mode = 'full'
    ): void {
        $guard = "\\Intranet\\Entities\\{$tabla['nombreclase']}::unguard";
        call_user_func($guard);
        $clase = "\\Intranet\\Entities\\{$tabla['nombreclase']}";
        $existingRecords = $this->preloadExistingRecords($xmltable, $tabla, $extractField, $clase);

        foreach ($xmltable->children() as $registroxml) {
            $atributosxml = $registroxml->attributes();
            $pasa = true;

            if (isset($tabla['filtro'])) {
                $pasa = $passesFilter($tabla['filtro'], $atributosxml);
            }
            if (isset($tabla['required'])) {
                $pasa = $pasa && $requiredCheck($tabla['required'], $atributosxml);
            }
            if (!$pasa) {
                continue;
            }

            $clave = $extractField($atributosxml, $tabla['id'], 0);
            $cacheKey = $this->normalizeCacheKey($clave);
            $record = $cacheKey !== null ? ($existingRecords[$cacheKey] ?? null) : $clase::find($clave);

            if ($record) {
                if ($mode === 'create_only' && (string) $tabla['nombreclase'] === 'Profesor') {
                    continue;
                }

                foreach ($tabla['update'] as $keybd => $keyxml) {
                    $record->{$keybd} = $extractField($atributosxml, $keyxml, 1);
                }
                $record->save();
                if ($cacheKey !== null) {
                    $existingRecords[$cacheKey] = $record;
                }
                continue;
            }

            $arrayDatos = [];
            foreach ($tabla['update'] + $tabla['create'] as $keybd => $keyxml) {
                $arrayDatos[$keybd] = $extractField($atributosxml, $keyxml, 1);
            }

            try {
                $created = $this->createRecordByClass((string) $tabla['nombreclase'], $arrayDatos, $idProfesor);
                if ($cacheKey !== null && $created !== null) {
                    $existingRecords[$cacheKey] = $created;
                }
            } catch (\Illuminate\Database\QueryException $e) {
                Alert::error($e->getMessage());
            }
        }

        Alert::success($tabla['nombrexml'] . ' con ' . count($xmltable->children()) . ' Registres');
    }

    /**
     * @param array<string, mixed> $arrayDatos
     */
    private function createRecordByClass(string $className, array $arrayDatos, string $idProfesor): mixed
    {
        switch ($className) {
            case 'Horario':
                if (($arrayDatos['plantilla'] ?? 0) >= $this->plantilla && ($arrayDatos['idProfesor'] ?? null) === $idProfesor) {
                    $this->plantilla = (int) $arrayDatos['plantilla'];
                    try {
                        $this->horarioService->create($arrayDatos);
                    } catch (\Illuminate\Database\QueryException) {
                        unset($arrayDatos['aula']);
                        $this->horarioService->create($arrayDatos);
                    }
                }
                return null;

            case 'Profesor':
                if (($arrayDatos['dni'] ?? null) === $idProfesor) {
                    return $this->profesorService->create($arrayDatos);
                }
                return null;
        }

        return null;
    }

    /**
     * @param callable(mixed, mixed, int): mixed $extractField
     * @return array<string, mixed>
     */
    private function preloadExistingRecords(mixed $xmltable, array $tabla, callable $extractField, string $class): array
    {
        if (($tabla['id'] ?? '') === '') {
            return [];
        }

        $keys = [];
        foreach ($xmltable->children() as $registroxml) {
            $attributes = $registroxml->attributes();
            $rawKey = $extractField($attributes, $tabla['id'], 0);
            $cacheKey = $this->normalizeCacheKey($rawKey);
            if ($cacheKey !== null) {
                $keys[$cacheKey] = true;
            }
        }

        if ($keys === []) {
            return [];
        }

        $model = new $class();
        $keyName = $model->getKeyName();
        $records = [];
        foreach (array_chunk(array_keys($keys), 500) as $chunk) {
            foreach ($class::query()->whereIn($keyName, $chunk)->get() as $record) {
                $records[(string) $record->getKey()] = $record;
            }
        }

        return $records;
    }

    private function normalizeCacheKey(mixed $key): ?string
    {
        if (is_array($key) || $key === '' || $key === null) {
            return null;
        }

        return (string) $key;
    }
}
