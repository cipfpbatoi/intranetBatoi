<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Espacio;
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
        private readonly GrupoService $grupoService,
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
        callable $requiredCheck,
        string $mode = 'full'
    ): void {
        if (is_file(storage_path() . '/logs/import.log')) {
            unlink(storage_path() . '/logs/import.log');
        }

        $this->log = new Logger('Import');
        $this->log->pushHandler(new StreamHandler(storage_path() . '/logs/import.log', Logger::DEBUG));

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
            if (!is_array($clave) && $this->log !== null) {
                $this->log->info("Processant {$clase}: {$clave}");
            }

            $cacheKey = $this->normalizeCacheKey($clave);
            $record = $cacheKey !== null ? ($existingRecords[$cacheKey] ?? null) : $clase::find($clave);
            if ($record) {
                if ($mode === 'create_only' && in_array((string) $tabla['nombreclase'], ['Alumno', 'Profesor'], true)) {
                    continue;
                }

                foreach ($tabla['update'] as $keybd => $keyxml) {
                    $record->{$keybd} = $extractField($atributosxml, $keyxml, 1);
                }

                try {
                    $record->save();
                    if ($cacheKey !== null) {
                        $existingRecords[$cacheKey] = $record;
                    }
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
                $created = $this->createRecordByClass((string) $tabla['nombreclase'], $arrayDatos);
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
    private function createRecordByClass(string $className, array $arrayDatos): mixed
    {
        switch ($className) {
            case 'Horario':
                $this->createHorario($arrayDatos);
                return null;
            case 'Alumno':
                return Alumno::create($arrayDatos);
            case 'Profesor':
                return $this->profesorService->create($arrayDatos);
            case 'Modulo':
                return Modulo::create($arrayDatos);
            case 'Ocupacion':
                return Ocupacion::create($arrayDatos);
            case 'AlumnoGrupo':
                return AlumnoGrupo::create($arrayDatos);
            case 'Grupo':
                return $this->grupoService->create($arrayDatos);
            case 'Espacio':
                return Espacio::create($arrayDatos);
        }

        return null;
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
        $horarios = $this->horarioService->forProgramacionImport();
        $validHorarios = [];
        $pairsByKey = [];
        $grupos = [];
        $profesoresDni = [];

        foreach ($horarios as $horario) {
            if (!isset($horario->Grupo->idCiclo)) {
                Alert::danger(($horario->idGrupo ?? 'GRUP DESCONEGUT') . ' sin ciclo');
                continue;
            }

            $validHorarios[] = $horario;
            $pairKey = $horario->modulo . '|' . $horario->Grupo->idCiclo;
            $pairsByKey[$pairKey] = ['idModulo' => $horario->modulo, 'idCiclo' => $horario->Grupo->idCiclo];
            $grupos[(string) $horario->idGrupo] = true;
            $profesoresDni[(string) $horario->idProfesor] = true;
        }

        if ($validHorarios === []) {
            return;
        }

        $departamentoByProfesor = $this->profesorService
            ->byDnis(array_keys($profesoresDni))
            ->pluck('departamento', 'dni')
            ->all();

        $modulos = array_values(array_unique(array_column($pairsByKey, 'idModulo')));
        $ciclos = array_values(array_unique(array_column($pairsByKey, 'idCiclo')));
        $moduloCiclos = Modulo_ciclo::query()
            ->whereIn('idModulo', $modulos)
            ->whereIn('idCiclo', $ciclos)
            ->get();

        $moduloCicloByPair = [];
        foreach ($moduloCiclos as $moduloCiclo) {
            $pairKey = $moduloCiclo->idModulo . '|' . $moduloCiclo->idCiclo;
            $moduloCicloByPair[$pairKey] = $moduloCiclo;
        }

        $moduloGrupoPairs = [];
        if ($moduloCiclos->isNotEmpty()) {
            $existingModuloGrupos = Modulo_grupo::query()
                ->whereIn('idModuloCiclo', $moduloCiclos->pluck('id')->all())
                ->whereIn('idGrupo', array_keys($grupos))
                ->get(['idModuloCiclo', 'idGrupo']);

            foreach ($existingModuloGrupos as $moduloGrupo) {
                $moduloGrupoPairs[$moduloGrupo->idModuloCiclo . '|' . $moduloGrupo->idGrupo] = true;
            }
        }

        foreach ($validHorarios as $horario) {
            $pairKey = $horario->modulo . '|' . $horario->Grupo->idCiclo;
            $moduloCiclo = $moduloCicloByPair[$pairKey] ?? null;
            $departamentoProfesor = $departamentoByProfesor[(string) $horario->idProfesor] ?? null;

            if (!$moduloCiclo) {
                $moduloCiclo = $this->createModuloCiclo($horario, $departamentoProfesor);
                $moduloCicloByPair[$pairKey] = $moduloCiclo;
            } elseif ((string) $moduloCiclo->idDepartamento === '99' && $departamentoProfesor !== null && (string) $departamentoProfesor !== '99') {
                $moduloCiclo->idDepartamento = $departamentoProfesor;
                $moduloCiclo->save();
            }

            $moduloGrupoKey = $moduloCiclo->id . '|' . $horario->idGrupo;
            if (isset($moduloGrupoPairs[$moduloGrupoKey])) {
                continue;
            }

            $nuevo = new Modulo_grupo();
            $nuevo->idModuloCiclo = $moduloCiclo->id;
            $nuevo->idGrupo = $horario->idGrupo;
            $nuevo->save();
            $moduloGrupoPairs[$moduloGrupoKey] = true;
        }
    }

    private function createModuloCiclo(mixed $horario, mixed $departamentoProfesor = null): Modulo_ciclo
    {
        $moduloCiclo = new Modulo_ciclo();
        $moduloCiclo->idModulo = $horario->modulo;
        $moduloCiclo->idCiclo = $horario->Grupo->idCiclo;
        $moduloCiclo->curso = $horario->Grupo->curso;
        $moduloCiclo->idDepartamento = $departamentoProfesor ?? '99';
        $moduloCiclo->save();

        return $moduloCiclo;
    }

    private function markAllAlumnosAsBaja(): void
    {
        DB::table('alumnos')->whereNull('baja')->update(['baja' => Hoy()]);
    }

    private function cleanupSustituciones(): void
    {
        $sustitutos = $this->profesorService->withSustituyeAssigned();
        $sustituidosDni = [];
        foreach ($sustitutos as $sustituto) {
            $dni = trim((string) $sustituto->sustituye_a);
            if ($dni !== '') {
                $sustituidosDni[$dni] = true;
            }
        }

        if ($sustituidosDni === []) {
            return;
        }

        $sustituidos = $this->profesorService->byDnis(array_keys($sustituidosDni))->keyBy('dni');
        foreach ($sustitutos as $sustituto) {
            $sustituido = $sustituidos->get((string) $sustituto->sustituye_a);
            if ($sustituido && $sustituido->fecha_baja === null) {
                $sustituto->sustituye_a = '';
                $sustituto->save();
            }
        }
    }

    private function assignDepartamentoByHorario(): void
    {
        $profesores = $this->profesorService->byDepartamento(99);
        if ($profesores->isEmpty()) {
            return;
        }

        $profesoresDni = $profesores->pluck('dni')->all();
        $horarios = DB::table('horarios')
            ->select('idProfesor', 'modulo', 'id')
            ->whereIn('idProfesor', $profesoresDni)
            ->whereNull('ocupacion')
            ->where('modulo', '!=', 'TU02CF')
            ->where('modulo', '!=', 'TU01CF')
            ->orderBy('id')
            ->get();

        $firstModuloByProfesor = [];
        foreach ($horarios as $horario) {
            if (!isset($firstModuloByProfesor[$horario->idProfesor])) {
                $firstModuloByProfesor[$horario->idProfesor] = $horario->modulo;
            }
        }

        if ($firstModuloByProfesor === []) {
            return;
        }

        $departamentoByModulo = DB::table('modulo_ciclos')
            ->join('ciclos', 'modulo_ciclos.idCiclo', '=', 'ciclos.id')
            ->whereIn('modulo_ciclos.idModulo', array_values(array_unique(array_values($firstModuloByProfesor))))
            ->select('modulo_ciclos.idModulo', 'ciclos.departamento')
            ->get()
            ->pluck('departamento', 'idModulo')
            ->all();

        foreach ($profesores as $profesor) {
            $modulo = $firstModuloByProfesor[$profesor->dni] ?? null;
            if ($modulo === null) {
                continue;
            }

            $departamento = $departamentoByModulo[$modulo] ?? null;
            if ($departamento !== null) {
                $profesor->departamento = $departamento;
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
