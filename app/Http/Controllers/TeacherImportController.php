<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intranet\Application\Import\Concerns\SharedImportFieldTransformers;
use Intranet\Application\Import\ImportSchemaProvider;
use Intranet\Application\Import\ImportService;
use Intranet\Application\Import\ImportWorkflowService;
use Intranet\Application\Import\ImportXmlHelperService;
use Intranet\Application\Import\TeacherImportExecutionService;
use Intranet\Entities\ImportRun;
use Intranet\Http\Requests\TeacherImportStoreRequest;
use Intranet\Jobs\RunImportJob;
use Styde\Html\Facades\Alert;

/**
 * Controlador d'importació individual de professorat i horaris.
 */
class TeacherImportController extends Seeder
{
    use SharedImportFieldTransformers;

    private ?ImportService $importService = null;
    private ?ImportWorkflowService $importWorkflowService = null;
    private ?ImportXmlHelperService $importXmlHelperService = null;
    private ?ImportSchemaProvider $importSchemaProvider = null;
    private ?TeacherImportExecutionService $teacherImportExecutionService = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $camposBdXml = [];

    public function create()
    {
        $this->authorizeImportManagement();
        return view('seeder.createTeacher');
    }

    public function store(Request $request)
    {
        $this->authorizeImportManagement();
        $request->merge([
            'idProfesor' => $this->normalizeProfesorId((string) $request->input('idProfesor', '')),
        ]);
        $this->normalizeBooleanInputs($request);
        Validator::make($request->all(), (new TeacherImportStoreRequest())->rules())->validate();
        $file = $this->imports()->resolveXmlFile($request);
        if ($file === null) {
            return back();
        }

        $mode = $this->resolveImportMode($request);
        Log::info('TeacherImportController@store', $this->importLogContext($request, [
            'mode' => $mode,
            'has_file' => $file !== null,
        ]));
        if ($mode === 'create_only') {
            return $this->executeSyncImport($file, $request);
        }

        return $this->storeAsync($request, $file);
    }

    public function storeAsync(Request $request, mixed $validatedFile = null)
    {
        $this->authorizeImportManagement();
        $request->merge([
            'idProfesor' => $this->normalizeProfesorId((string) $request->input('idProfesor', '')),
        ]);
        $this->normalizeBooleanInputs($request);
        $file = $validatedFile ?? $this->imports()->resolveXmlFile($request);
        if ($file === null) {
            return back();
        }

        $mode = $this->resolveImportMode($request);
        $hasImportRunsTable = Schema::hasTable('import_runs');
        Log::info('TeacherImportController@storeAsync', $this->importLogContext($request, [
            'mode' => $mode,
            'has_import_runs_table' => $hasImportRunsTable,
            'queue_default' => (string) config('queue.default', 'sync'),
            'jobs_table_exists' => Schema::hasTable((string) config('queue.connections.database.table', 'jobs')),
        ]));

        if ($mode === 'create_only') {
            return $this->executeSyncImport($file, $request);
        }

        if (!$hasImportRunsTable) {
            Log::warning('TeacherImportController falls back to sync because import_runs table is not visible.', $this->importLogContext($request));
            Alert::warning("No existeix la taula import_runs. L'import s'executarà en mode sincrònic i no quedarà registrat en l'historial.");
            return $this->executeSyncImport($file, $request);
        }

        $storedPath = Storage::disk('local')->putFileAs(
            'imports',
            $file,
            uniqid('teacher_', true) . '.xml'
        );

        $importRun = ImportRun::create([
            'type' => 'teacher',
            'status' => 'pending',
            'file_path' => $storedPath,
            'options' => [
                'idProfesor' => (string) $request->idProfesor,
                'horari' => (bool) $request->horari,
                'lost' => (bool) $request->lost,
                'mode' => $mode,
            ],
            'progress' => 0,
            'message' => 'Importació professorat en cua',
        ]);
        Log::info('TeacherImportController created import run.', $this->importLogContext($request, [
            'import_run_id' => $importRun->id,
            'stored_path' => $storedPath,
        ]));

        $dispatchSync = $this->shouldDispatchImportSync();
        if ($dispatchSync) {
            RunImportJob::dispatchSync($importRun->id);
        } else {
            RunImportJob::dispatch($importRun->id);
        }
        Log::info('TeacherImportController dispatched import job.', $this->importLogContext($request, [
            'import_run_id' => $importRun->id,
            'dispatch_sync' => $dispatchSync,
        ]));

        if ($dispatchSync) {
            Alert::info('Importació professorat executada en esta petició. ID: ' . $importRun->id);
        } else {
            Alert::info('Importació professorat encolada. ID: ' . $importRun->id);
        }

        return view('seeder.store', ['importRunId' => $importRun->id]);
    }

    /**
     * Executa la importació individual de professorat i, si cal, la
     * substitució segura del seu horari.
     */
    public function run($fxml, Request $request)
    {
        $this->authorizeImportManagement(true);
        $idProfesor = $this->normalizeProfesorId((string) $request->input('idProfesor', ''));
        $request->merge(['idProfesor' => $idProfesor]);
        $this->normalizeBooleanInputs($request);
        $execution = $this->executions();

        if ($request->horari) {
            $execution->prepareTeacherHorarios($idProfesor, (bool) $request->lost);
        }

        $this->workflows()->executeXmlImportSimple(
            $fxml,
            $this->camposBdXml(),
            $idProfesor,
            function ($xmltable, $table, $idProfesor) use ($execution, $request): void {
                $execution->importTable(
                    $xmltable,
                    $table,
                    (string) $idProfesor,
                    fn ($atrxml, $llave, $func = 1) => $this->sacaCampos($atrxml, $llave, $func),
                    fn ($filtro, $campos) => $this->filtro($filtro, $campos),
                    fn ($required, $campos) => $this->required($required, $campos),
                    $this->resolveImportMode($request),
                );
            }
        );

        if ($request->horari) {
            $execution->finalizeTeacherHorarios();
        }

        return [
            'message' => $execution->pullStatusMessage(),
        ];
    }

    private function sacaCampos($atrxml, $llave, $func = 1)
    {
        return $this->xmlHelper()->extractField($atrxml, $llave, $func, $this);
    }

    private function filtro($filtro, $campos)
    {
        return $this->xmlHelper()->passesFilter($filtro, $campos);
    }

    private function required($required, $campos)
    {
        $missing = $this->xmlHelper()->findMissingRequired($required, $campos, true);
        if ($missing !== null) {
            Alert::danger('Camp buid: ' . print_r($campos, true));
            return false;
        }

        return true;
    }

    private function imports(): ImportService
    {
        if ($this->importService === null) {
            $this->importService = app(ImportService::class);
        }

        return $this->importService;
    }

    private function workflows(): ImportWorkflowService
    {
        if ($this->importWorkflowService === null) {
            $this->importWorkflowService = app(ImportWorkflowService::class);
        }

        return $this->importWorkflowService;
    }

    private function schemas(): ImportSchemaProvider
    {
        if ($this->importSchemaProvider === null) {
            $this->importSchemaProvider = app(ImportSchemaProvider::class);
        }

        return $this->importSchemaProvider;
    }

    private function xmlHelper(): ImportXmlHelperService
    {
        if ($this->importXmlHelperService === null) {
            $this->importXmlHelperService = app(ImportXmlHelperService::class);
        }

        return $this->importXmlHelperService;
    }

    private function executions(): TeacherImportExecutionService
    {
        if ($this->teacherImportExecutionService === null) {
            $this->teacherImportExecutionService = app(TeacherImportExecutionService::class);
        }

        return $this->teacherImportExecutionService;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function camposBdXml(): array
    {
        if ($this->camposBdXml === []) {
            $this->camposBdXml = $this->schemas()->forTeacherImport();
        }

        return $this->camposBdXml;
    }

    private function executeSyncImport(mixed $file, Request $request)
    {
        Log::info('TeacherImportController executing sync import.', $this->importLogContext($request, [
            'mode' => $this->resolveImportMode($request),
            'idProfesor' => (string) $request->input('idProfesor', ''),
            'horari' => (bool) $request->boolean('horari'),
            'lost' => (bool) $request->boolean('lost'),
        ]));
        $this->imports()->runWithExtendedTimeout(function ($importFile, $importRequest): void {
            $this->run($importFile, $importRequest);
        }, $file, $request);

        return view('seeder.store');
    }

    private function resolveImportMode(Request $request): string
    {
        $mode = (string) $request->input('mode', 'full');

        return in_array($mode, ['full', 'create_only'], true) ? $mode : 'full';
    }

    /**
     * Normalitza el camp del professor per a tolerar espais residuals o text afegit.
     */
    private function normalizeProfesorId(string $idProfesor): string
    {
        $normalized = preg_replace('/\x{00A0}/u', ' ', $idProfesor) ?? $idProfesor;
        $normalized = trim($normalized);

        if ($normalized === '') {
            return '';
        }

        $parts = preg_split('/\s+/u', $normalized);

        return strtoupper((string) ($parts[0] ?? ''));
    }

    /**
     * Normalitza els checkbox HTML a booleans reals abans de validar o executar.
     */
    private function normalizeBooleanInputs(Request $request): void
    {
        $request->merge([
            'horari' => $request->boolean('horari'),
            'lost' => $request->boolean('lost'),
        ]);
    }

    /**
     * En local, si la cua usa base de dades, executem el job al moment per a
     * no dependre d'un worker separat que pot no existir en Docker.
     */
    private function shouldDispatchImportSync(): bool
    {
        $defaultQueue = (string) config('queue.default', 'sync');
        if ($defaultQueue === 'sync') {
            return true;
        }

        if ($defaultQueue !== 'database') {
            return false;
        }

        $jobsTable = (string) config('queue.connections.database.table', 'jobs');
        if (!Schema::hasTable($jobsTable)) {
            return true;
        }

        return app()->environment('local');
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function importLogContext(Request $request, array $extra = []): array
    {
        return array_merge([
            'request_mode' => (string) $request->input('mode', ''),
            'request_id_profesor' => (string) $request->input('idProfesor', ''),
            'request_horari' => $request->input('horari'),
            'request_lost' => $request->input('lost'),
            'database' => DB::connection()->getDatabaseName(),
            'app_env' => app()->environment(),
        ], $extra);
    }

    private function authorizeImportManagement(bool $allowConsole = false): void
    {
        if (defined('PHPUNIT_COMPOSER_INSTALL') || app()->runningUnitTests() || app()->environment('testing')) {
            return;
        }

        if ($allowConsole && app()->runningInConsole()) {
            return;
        }

        Gate::authorize('manage', ImportRun::class);
    }
}
