<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Espacio;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Ocupacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Styde\Html\Facades\Alert;

class GeneralImportExecutionService
{
    private int $plantilla = 0;

    private ?Logger $log = null;

    public function __construct(
        private readonly HorarioService $horarioService,
        private readonly ProfesorService $profesorService,
    ) {
    }

    public function handlePreImport(string $className, string $xmlName): void
    {
        switch ($className) {
            case 'Alumno':
                $this->markAllAlumnosAsBaja();
                break;
            case 'Profesor':
                $this->disableProfesores();
                break;
            case 'Grupo':
                $this->markAllGruposWithoutTutor();
                break;
            case 'AlumnoGrupo':
                $this->cloneTable('alumnos_grupos');
                $this->truncateTables('alumnos_grupos');
                break;
            case 'Horario':
                $this->plantilla = (int) (DB::table('horarios')->orderBy('plantilla', 'desc')->value('plantilla') ?? 0);
                if ($xmlName === 'horarios_grupo') {
                    $this->truncateTables('horarios');
                }
                break;
        }
    }

    public function handlePostImport(string $className, string $xmlName, mixed $firstImport): void
    {
        switch ($className) {
            case 'Profesor':
                $this->cleanupSustituciones();
                $this->assignDepartamentoByHorario();
                break;
            case 'Alumno':
                $this->removeBajaAlumnosFromGroups();
                break;
            case 'Grupo':
                if ($firstImport) {
                    $this->deleteBajaGrupos();
                }
                $this->normalizeEmptyTutor();
                break;
            case 'AlumnoGrupo':
                $this->deleteBlankRecords('alumnos_grupos', 'idGrupo');
                $this->restoreAlumnosGrupoCopy();
                break;
            case 'Horario':
                if ($xmlName === 'horarios_ocupaciones') {
                    $this->keepLatestHorarioPlantilla();
                    if ($firstImport) {
                        $this->createModuloCicloAndGrupoFromHorarios();
                    }
                }
                break;
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
        callable $extractField,
        callable $passesFilter,
        callable $requiredCheck
    ): void {
        if (is_file(storage_path() . '/logs/import.log')) {
            unlink(storage_path() . '/logs/import.log');
        }

        $this->log = new Logger('Import');
        $this->log->pushHandler(new StreamHandler(storage_path() . '/logs/import.log', Logger::DEBUG));

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
            if (!is_array($clave) && $this->log !== null) {
                $this->log->info("Processant {$clase}: {$clave}");
            }

            $record = $clase::find($clave);
            if ($record) {
                foreach ($tabla['update'] as $keybd => $keyxml) {
                    $record->{$keybd} = $extractField($atributosxml, $keyxml, 1);
                }

                try {
                    $record->save();
                } catch (\Illuminate\Database\QueryException $e) {
                    Alert::error($e->getMessage());
                }

                continue;
            }

            $arrayDatos = [];
            foreach ($tabla['update'] + $tabla['create'] as $keybd => $keyxml) {
                $arrayDatos[$keybd] = $extractField($atributosxml, $keyxml, 1);
            }

            try {
                $this->createRecordByClass((string) $tabla['nombreclase'], $arrayDatos);
            } catch (\Illuminate\Database\QueryException $e) {
                Alert::error($e->getMessage());
            }
        }

        Alert::success($tabla['nombrexml'] . ' con ' . count($xmltable->children()) . ' Registres');
    }

    /**
     * @param array<string, mixed> $arrayDatos
     */
    private function createRecordByClass(string $className, array $arrayDatos): void
    {
        switch ($className) {
            case 'Horario':
                $this->createHorario($arrayDatos);
                break;
            case 'Alumno':
                Alumno::create($arrayDatos);
                break;
            case 'Profesor':
                $this->profesorService->create($arrayDatos);
                break;
            case 'Modulo':
                Modulo::create($arrayDatos);
                break;
            case 'Ocupacion':
                Ocupacion::create($arrayDatos);
                break;
            case 'AlumnoGrupo':
                AlumnoGrupo::create($arrayDatos);
                break;
            case 'Grupo':
                Grupo::create($arrayDatos);
                break;
            case 'Espacio':
                Espacio::create($arrayDatos);
                break;
        }
    }

    /**
     * @param array<string, mixed> $arrayDatos
     */
    private function createHorario(array $arrayDatos): void
    {
        if (($arrayDatos['plantilla'] ?? 0) < $this->plantilla) {
            return;
        }

        $this->plantilla = (int) $arrayDatos['plantilla'];
        try {
            $this->horarioService->create($arrayDatos);
        } catch (\Illuminate\Database\QueryException) {
            unset($arrayDatos['aula']);
            $this->horarioService->create($arrayDatos);
        }
    }

    private function createModuloCicloAndGrupoFromHorarios(): void
    {
        foreach ($this->horarioService->forProgramacionImport() as $horario) {
            if (!isset($horario->Grupo->idCiclo)) {
                Alert::danger($horario->Grupo->id . ' sin ciclo');
                continue;
            }

            $moduloCiclo = Modulo_ciclo::where('idModulo', $horario->modulo)
                ->where('idCiclo', $horario->Grupo->idCiclo)
                ->first();

            if (!$moduloCiclo) {
                $moduloCiclo = $this->createModuloCiclo($horario);
            } else {
                $this->updateDepartamentoIfNeeded($moduloCiclo, (string) $horario->idProfesor);
            }

            if (Modulo_grupo::where('idModuloCiclo', $moduloCiclo->id)->where('idGrupo', $horario->idGrupo)->count() === 0) {
                $nuevo = new Modulo_grupo();
                $nuevo->idModuloCiclo = $moduloCiclo->id;
                $nuevo->idGrupo = $horario->idGrupo;
                $nuevo->save();
            }
        }
    }

    private function createModuloCiclo(mixed $horario): Modulo_ciclo
    {
        $moduloCiclo = new Modulo_ciclo();
        $moduloCiclo->idModulo = $horario->modulo;
        $moduloCiclo->idCiclo = $horario->Grupo->idCiclo;
        $moduloCiclo->curso = $horario->Grupo->curso;

        $profesor = $this->profesorService->find((string) $horario->idProfesor);
        $moduloCiclo->idDepartamento = isset($profesor->departamento) ? $profesor->departamento : '99';
        $moduloCiclo->save();

        return $moduloCiclo;
    }

    private function updateDepartamentoIfNeeded(Modulo_ciclo $moduloCiclo, string $dniProfesor): void
    {
        $profesor = $this->profesorService->find($dniProfesor);
        if (isset($profesor->departamento) && (string) $moduloCiclo->idDepartamento === '99') {
            $moduloCiclo->idDepartamento = $profesor->departamento;
            $moduloCiclo->save();
        }
    }

    private function markAllAlumnosAsBaja(): void
    {
        DB::table('alumnos')->whereNull('baja')->update(['baja' => Hoy()]);
    }

    private function cleanupSustituciones(): void
    {
        foreach ($this->profesorService->withSustituyeAssigned() as $sustituto) {
            $sustituido = $this->profesorService->find((string) $sustituto->sustituye_a);
            if ($sustituido && $sustituido->fecha_baja === null) {
                $sustituto->sustituye_a = '';
                $sustituto->save();
            }
        }
    }

    private function assignDepartamentoByHorario(): void
    {
        foreach ($this->profesorService->byDepartamento(99) as $profesor) {
            $horario = $this->horarioService->firstForDepartamentoAsignacion((string) $profesor->dni);
            if (!$horario) {
                continue;
            }

            $modulo = Modulo_ciclo::where('idModulo', $horario->modulo)->first();
            if ($modulo) {
                $profesor->departamento = $modulo->Ciclo->departamento;
                $profesor->save();
            }
        }
    }

    private function disableProfesores(): void
    {
        DB::table('profesores')->update(['activo' => false, 'sustituye_a' => '']);
    }

    private function removeBajaAlumnosFromGroups(): void
    {
        DB::table('alumnos_grupos')->join('alumnos', 'idAlumno', '=', 'nia')->whereNotNull('alumnos.baja')->delete();
    }

    private function markAllGruposWithoutTutor(): void
    {
        DB::table('grupos')->update(['tutor' => 'BAJA']);
    }

    private function deleteBajaGrupos(): void
    {
        DB::table('grupos')->where('tutor', '=', 'BAJA')->delete();
    }

    private function normalizeEmptyTutor(): void
    {
        DB::table('grupos')->where('tutor', '=', ' ')->update(['tutor' => 'SIN TUTOR']);
    }

    /**
     * @param array<int, string>|string $tables
     */
    private function truncateTables(array|string $tables): void
    {
        $this->setForeignKeys(false);

        if (is_array($tables)) {
            foreach ($tables as $tabla) {
                DB::table($tabla)->truncate();
            }
        } else {
            DB::table($tables)->truncate();
        }

        $this->setForeignKeys(true);
    }

    private function cloneTable(string $table): void
    {
        DB::statement("DROP table IF exists tmp_{$table};");
        DB::statement("CREATE TABLE tmp_{$table} LIKE {$table};");
        DB::statement("INSERT INTO tmp_{$table} SELECT * FROM {$table};");
    }

    private function deleteBlankRecords(string $table, string $column): void
    {
        DB::table($table)->where($column, '=', '')->delete();
    }

    private function restoreAlumnosGrupoCopy(): void
    {
        $tmpAll = DB::select('select * from tmp_alumnos_grupos where subGrupo IS NOT NULL');
        foreach ($tmpAll as $registro) {
            $find = AlumnoGrupo::where('idAlumno', $registro->idAlumno)
                ->where('idGrupo', $registro->idGrupo)
                ->first();
            if ($find) {
                $find->subGrupo = $registro->subGrupo;
                $find->posicion = $registro->posicion;
                $find->save();
            }
        }

        DB::statement('DROP table IF exists tmp_alumnos_grupos');
    }

    private function keepLatestHorarioPlantilla(): void
    {
        $ultimoHorario = DB::table('horarios')->orderBy('plantilla', 'desc')->first();
        if ($ultimoHorario) {
            DB::table('horarios')->where('plantilla', '<>', $ultimoHorario->plantilla)->delete();
        }
    }

    private function setForeignKeys(bool $enabled): void
    {
        $value = $enabled ? '1' : '0';
        DB::statement("SET FOREIGN_KEY_CHECKS= {$value};");
    }

    public function loadEstadoFromHorarioJson(): void
    {
        foreach ($this->profesorService->activos() as $profesor) {
            $path = '/horarios/' . $profesor->dni . '.json';
            if (!Storage::disk('local')->exists($path)) {
                continue;
            }

            $fichero = Storage::disk('local')->get($path);
            if ($fichero && json_decode($fichero)->estado === 'Guardado') {
                session([$profesor->dni => 1]);
            }
        }
    }
}
