<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Support\Facades\DB;
use Styde\Html\Facades\Alert;

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
        callable $requiredCheck
    ): void {
        $guard = "\\Intranet\\Entities\\{$tabla['nombreclase']}::unguard";
        call_user_func($guard);

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

            $clase = "\\Intranet\\Entities\\{$tabla['nombreclase']}";
            $clave = $extractField($atributosxml, $tabla['id'], 0);
            $record = $clase::find($clave);

            if ($record) {
                foreach ($tabla['update'] as $keybd => $keyxml) {
                    $record->{$keybd} = $extractField($atributosxml, $keyxml, 1);
                }
                $record->save();
                continue;
            }

            $arrayDatos = [];
            foreach ($tabla['update'] + $tabla['create'] as $keybd => $keyxml) {
                $arrayDatos[$keybd] = $extractField($atributosxml, $keyxml, 1);
            }

            try {
                $this->createRecordByClass((string) $tabla['nombreclase'], $arrayDatos, $idProfesor);
            } catch (\Illuminate\Database\QueryException $e) {
                Alert::error($e->getMessage());
            }
        }

        Alert::success($tabla['nombrexml'] . ' con ' . count($xmltable->children()) . ' Registres');
    }

    /**
     * @param array<string, mixed> $arrayDatos
     */
    private function createRecordByClass(string $className, array $arrayDatos, string $idProfesor): void
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
                break;

            case 'Profesor':
                if (($arrayDatos['dni'] ?? null) === $idProfesor) {
                    $this->profesorService->create($arrayDatos);
                }
                break;
        }
    }
}
