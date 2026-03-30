<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Styde\Html\Facades\Alert;

/**
 * Execució de la importació individual de professorat.
 *
 * En la substitució d'horaris es prepara primer el nou conjunt de files i
 * només s'esborra l'horari actual quan hi ha dades vàlides per al professor.
 */
class TeacherImportExecutionService
{
    private int $plantilla = 0;
    private bool $replaceTeacherHorarios = false;
    private string $targetProfesor = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $pendingHorarios = [];

    private int $matchedHorarioRows = 0;
    private int $skippedHorarioRows = 0;

    public function __construct(
        private readonly HorarioService $horarioService,
        private readonly ProfesorService $profesorService,
    ) {
    }

    /**
     * Prepara la substitució segura de l'horari d'un professor.
     *
     * Per a una importació individual, la `plantilla` de referència ha de ser
     * la de l'últim horari del mateix professor, no la màxima global.
     */
    public function prepareTeacherHorarios(string $idProfesor, bool $lost = false): void
    {
        $this->replaceTeacherHorarios = true;
        $this->targetProfesor = $idProfesor;
        $this->pendingHorarios = [];
        $this->matchedHorarioRows = 0;
        $this->skippedHorarioRows = 0;
        $this->plantilla = $this->resolveTeacherPlantilla($idProfesor, $lost);
    }

    /**
     * Aplica els horaris preparats, preservant l'horari existent si no hi ha
     * cap fila nova vàlida per al professor.
     */
    public function finalizeTeacherHorarios(): void
    {
        if (!$this->replaceTeacherHorarios) {
            return;
        }

        try {
            if ($this->pendingHorarios === []) {
                $this->warnEmptyHorarioReplacement();
                return;
            }

            DB::transaction(function (): void {
                $this->horarioService->deleteByProfesor($this->targetProfesor);

                foreach ($this->pendingHorarios as $horario) {
                    $this->persistHorario($horario);
                }
            });
        } finally {
            $this->resetHorarioReplacementState();
        }
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
                if (($arrayDatos['idProfesor'] ?? null) !== $idProfesor) {
                    return null;
                }

                if ($this->replaceTeacherHorarios) {
                    $this->queueHorarioReplacement($arrayDatos);
                    return null;
                }

                $this->persistHorario($arrayDatos);
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

    /**
     * @param array<string, mixed> $arrayDatos
     */
    private function queueHorarioReplacement(array $arrayDatos): void
    {
        $this->matchedHorarioRows++;
        $plantilla = (int) ($arrayDatos['plantilla'] ?? 0);

        if ($plantilla < $this->plantilla) {
            $this->skippedHorarioRows++;
            return;
        }

        if ($plantilla > $this->plantilla) {
            $this->pendingHorarios = [];
            $this->plantilla = $plantilla;
        }

        $this->pendingHorarios[] = $arrayDatos;
    }

    /**
     * @param array<string, mixed> $arrayDatos
     */
    private function persistHorario(array $arrayDatos): void
    {
        try {
            $this->horarioService->create($arrayDatos);
        } catch (QueryException) {
            unset($arrayDatos['aula']);
            $this->horarioService->create($arrayDatos);
        }
    }

    private function resolveTeacherPlantilla(string $idProfesor, bool $lost): int
    {
        if ($lost) {
            return 0;
        }

        return (int) (DB::table('horarios')
            ->where('idProfesor', $idProfesor)
            ->orderBy('plantilla', 'desc')
            ->value('plantilla') ?? 0);
    }

    private function warnEmptyHorarioReplacement(): void
    {
        if ($this->matchedHorarioRows === 0) {
            Alert::warning("No s'han trobat horaris nous per al professor {$this->targetProfesor} en el fitxer.");
            return;
        }

        if ($this->skippedHorarioRows > 0) {
            Alert::warning(
                "No s'ha substituït l'horari del professor {$this->targetProfesor} perquè la plantilla importada és més antiga que l'actual."
            );
        }
    }

    private function resetHorarioReplacementState(): void
    {
        $this->replaceTeacherHorarios = false;
        $this->targetProfesor = '';
        $this->pendingHorarios = [];
        $this->matchedHorarioRows = 0;
        $this->skippedHorarioRows = 0;
        $this->plantilla = 0;
    }
}
