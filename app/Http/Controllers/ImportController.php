<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intranet\Application\Import\Concerns\SharedImportFieldTransformers;
use Intranet\Application\Import\GeneralImportExecutionService;
use Intranet\Application\Import\ImportSchemaProvider;
use Intranet\Application\Import\ImportService;
use Intranet\Application\Import\ImportWorkflowService;
use Intranet\Application\Import\ImportXmlHelperService;
use Intranet\Entities\ImportRun;
use Intranet\Http\Requests\ImportStoreRequest;
use Intranet\Jobs\RunImportJob;
use Styde\Html\Facades\Alert;

class ImportController extends Seeder
{
    use SharedImportFieldTransformers;

    private ?ImportService $importService = null;
    private ?ImportWorkflowService $importWorkflowService = null;
    private ?ImportXmlHelperService $importXmlHelperService = null;
    private ?ImportSchemaProvider $importSchemaProvider = null;
    private ?GeneralImportExecutionService $generalImportExecutionService = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $camposBdXml = [];

    public function create()
    {
        $this->authorizeImportManagement();
        return view('seeder.create');
    }

    public function store(Request $request)
    {
        $this->authorizeImportManagement();
        Validator::make($request->all(), (new ImportStoreRequest())->rules())->validate();
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
            uniqid('general_', true) . '.xml'
        );

        $importRun = ImportRun::create([
            'type' => 'general',
            'status' => 'pending',
            'file_path' => $storedPath,
            'options' => [
                'primera' => $request->primera,
                'mode' => $mode,
            ],
            'progress' => 0,
            'message' => 'Importació en cua',
        ]);

        if (config('queue.default') === 'database' && !Schema::hasTable((string) config('queue.connections.database.table', 'jobs'))) {
            RunImportJob::dispatchSync($importRun->id);
        } else {
            RunImportJob::dispatch($importRun->id);
        }

        Alert::info('Importació encolada. ID: ' . $importRun->id);

        return view('seeder.store', ['importRunId' => $importRun->id]);
    }

    public function history()
    {
        $this->authorizeImportManagement();
        if (!Schema::hasTable('import_runs')) {
            Alert::warning('No existeix la taula import_runs.');
            return view('seeder.history', ['runs' => collect()]);
        }

        $runs = ImportRun::query()->latest('id')->limit(100)->get();

        return view('seeder.history', ['runs' => $runs]);
    }

    public function status(int $importRunId)
    {
        $this->authorizeImportManagement();
        $run = ImportRun::findOrFail($importRunId);

        return response()->json([
            'id' => $run->id,
            'type' => $run->type,
            'status' => $run->status,
            'progress' => $run->progress,
            'message' => $run->message,
            'error' => $run->error,
            'started_at' => optional($run->started_at)->toDateTimeString(),
            'finished_at' => optional($run->finished_at)->toDateTimeString(),
            'failed_at' => optional($run->failed_at)->toDateTimeString(),
            'created_at' => optional($run->created_at)->toDateTimeString(),
        ]);
    }

    public function asignarTutores()
    {
        $this->authorizeImportManagement(true);
        $this->workflows()->assignTutores();
    }

    public function run($fxml, Request $request)
    {
        $this->authorizeImportManagement(true);
        $execution = $this->executions();

        $this->workflows()->executeXmlImportWithHooks(
            $fxml,
            $this->camposBdXml(),
            $request->primera,
            function ($clase, $xml) use ($execution): void {
                $execution->handlePreImport($clase, $xml);
            },
            function ($xmltable, $table) use ($execution, $request): void {
                $execution->importTable(
                    $xmltable,
                    $table,
                    fn ($atrxml, $llave, $func = 1) => $this->sacaCampos($atrxml, $llave, $func),
                    fn ($filtro, $campos) => $this->filtro($filtro, $campos),
                    fn ($required, $campos) => $this->required($required, $campos),
                    $this->resolveImportMode($request),
                );
            },
            function ($clase, $xml, $firstImport) use ($execution): void {
                $execution->handlePostImport($clase, $xml, $firstImport);
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
        $campBuid = $this->xmlHelper()->findMissingRequired($required, $campos, false);
        if ($campBuid !== null) {
            Alert::danger("Camp $campBuid buid");
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

    private function executions(): GeneralImportExecutionService
    {
        if ($this->generalImportExecutionService === null) {
            $this->generalImportExecutionService = app(GeneralImportExecutionService::class);
        }

        return $this->generalImportExecutionService;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function camposBdXml(): array
    {
        if ($this->camposBdXml === []) {
            $this->camposBdXml = $this->schemas()->forGeneralImport();
        }

        return $this->camposBdXml;
    }

    private function executeSyncImport(mixed $file, Request $request)
    {
        $this->imports()->runWithExtendedTimeout(function ($importFile, $importRequest): void {
            $this->run($importFile, $importRequest);
        }, $file, $request);

        if ($this->imports()->isFirstImport($request)) {
            $this->asignarTutores();
        }

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
