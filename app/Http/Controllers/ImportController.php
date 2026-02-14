<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Intranet\Application\Import\Concerns\SharedImportFieldTransformers;
use Intranet\Application\Import\GeneralImportExecutionService;
use Intranet\Application\Import\ImportSchemaProvider;
use Intranet\Application\Import\ImportService;
use Intranet\Application\Import\ImportWorkflowService;
use Intranet\Application\Import\ImportXmlHelperService;
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
        return view('seeder.create');
    }

    public function store(Request $request)
    {
        $file = $this->imports()->resolveXmlFile($request);
        if ($file === null) {
            return back();
        }

        $this->imports()->runWithExtendedTimeout(function ($importFile, $importRequest): void {
            $this->run($importFile, $importRequest);
        }, $file, $request);

        if ($this->imports()->isFirstImport($request)) {
            $this->asignarTutores();
        }

        return view('seeder.store');
    }

    public function asignarTutores()
    {
        $this->workflows()->assignTutores();
    }

    public function run($fxml, Request $request)
    {
        $execution = $this->executions();

        $this->workflows()->executeXmlImportWithHooks(
            $fxml,
            $this->camposBdXml(),
            $request->primera,
            function ($clase, $xml) use ($execution): void {
                $execution->handlePreImport($clase, $xml);
            },
            function ($xmltable, $table) use ($execution): void {
                $execution->importTable(
                    $xmltable,
                    $table,
                    fn ($atrxml, $llave, $func = 1) => $this->sacaCampos($atrxml, $llave, $func),
                    fn ($filtro, $campos) => $this->filtro($filtro, $campos),
                    fn ($required, $campos) => $this->required($required, $campos),
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
}
