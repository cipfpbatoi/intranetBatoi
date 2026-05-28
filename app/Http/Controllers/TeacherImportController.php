<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
use Intranet\Services\UI\AppAlert as Alert;

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
        Validator::make($request->all(), (new TeacherImportStoreRequest())->rules())->validate();
        $file = $this->imports()->resolveXmlFile($request);
        if ($file === null) {
            return back();
        }

        $mode = $this->resolveImportMode($request);
        if ($mode === 'create_only') {
            return $this->executeSyncImport($file, $request);
        }

        return $this->storeAsync($request, $file);
    }

    public function storeAsync(Request $request, mixed $validatedFile = null)
    {
        $this->authorizeImportManagement();
        $file = $validatedFile ?? $this->imports()->resolveXmlFile($request);
        if ($file === null) {
            return back();
        }

        $mode = $this->resolveImportMode($request);
        if ($mode === 'create_only') {
            return $this->executeSyncImport($file, $request);
        }

        if (!Schema::hasTable('import_runs')) {
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

        if (config('queue.default') === 'database' && !Schema::hasTable((string) config('queue.connections.database.table', 'jobs'))) {
            RunImportJob::dispatchSync($importRun->id);
        } else {
            RunImportJob::dispatch($importRun->id);
        }

        Alert::info('Importació professorat encolada. ID: ' . $importRun->id);

        return view('seeder.store', ['importRunId' => $importRun->id]);
    }

    public function run($fxml, Request $request)
    {
        $this->authorizeImportManagement(true);
        $execution = $this->executions();

        if ($request->horari) {
            $execution->clearTeacherHorarios((string) $request->idProfesor, (bool) $request->lost);
        }

        $this->workflows()->executeXmlImportSimple(
            $fxml,
            $this->camposBdXml(),
            $request->idProfesor,
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
