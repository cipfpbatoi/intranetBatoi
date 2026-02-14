<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Intranet\Application\Import\Concerns\SharedImportFieldTransformers;
use Intranet\Application\Import\ImportSchemaProvider;
use Intranet\Application\Import\ImportService;
use Intranet\Application\Import\ImportWorkflowService;
use Intranet\Application\Import\ImportXmlHelperService;
use Intranet\Application\Import\TeacherImportExecutionService;
use Styde\Html\Facades\Alert;

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
        return view('seeder.createTeacher');
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

        return view('seeder.store');
    }

    public function run($fxml, Request $request)
    {
        $execution = $this->executions();

        if ($request->horari) {
            $execution->clearTeacherHorarios((string) $request->idProfesor, (bool) $request->lost);
        }

        $this->workflows()->executeXmlImportSimple(
            $fxml,
            $this->camposBdXml(),
            $request->idProfesor,
            function ($xmltable, $table, $idProfesor) use ($execution): void {
                $execution->importTable(
                    $xmltable,
                    $table,
                    (string) $idProfesor,
                    fn ($atrxml, $llave, $func = 1) => $this->saca_campos($atrxml, $llave, $func),
                    fn ($filtro, $campos) => $this->filtro($filtro, $campos),
                    fn ($required, $campos) => $this->required($required, $campos),
                );
            }
        );
    }

    private function saca_campos($atrxml, $llave, $func = 1)
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
}
