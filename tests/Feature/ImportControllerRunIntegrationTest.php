<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\ImportController;
use Tests\TestCase;

class ImportControllerRunIntegrationTest extends TestCase
{
    private string $sqlitePath;
    private string $xmlPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('import_run_integration_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('espacios');
        Schema::connection('sqlite')->dropIfExists('grupos');

        if (isset($this->xmlPath) && file_exists($this->xmlPath)) {
            @unlink($this->xmlPath);
        }
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_run_importa_grup_i_aula_des_de_xml_valid(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <aulas>
    <aula codigo="A-213" nombre="A-213 - Aula de pissarra" capacidad="35" m2="60" observaciones="Edifici 2 - Planta 1"/>
  </aulas>
  <grupos>
    <grupo codigo="1CFSC" nombre="1R CFS ASIX -V- (LOE)" ensenanza="5" linea="1" turno="D" modalidad="COM" aula="T-117" capacidad="1" tutor_ppal="021648508B" tutor_sec=" " oficial="S"/>
  </grupos>
</centro>
XML;

        $this->xmlPath = storage_path('import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(ImportController::class);
        $request = Request::create('/import', 'POST', ['primera' => false]);

        $controller->run($this->xmlPath, $request);

        $grupo = DB::table('grupos')->where('codigo', '1CFSC')->first();
        $this->assertNotNull($grupo);
        $this->assertSame('1R CFS ASIX -V- (LOE)', $grupo->nombre);
        $this->assertSame('D', $grupo->turno);
        $this->assertSame('021648508B', $grupo->tutor);

        $espacio = DB::table('espacios')->where('aula', 'A-213')->first();
        $this->assertNotNull($espacio);
        $this->assertSame('A-213 - Aula de pissarra', $espacio->descripcion);
        $this->assertSame(99, (int) $espacio->idDepartamento);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('activities')) {
            Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->text('comentari')->nullable();
                $table->string('document')->nullable();
                $table->string('model_class')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('author_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('grupos')) {
            Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
                $table->string('codigo')->primary();
                $table->string('nombre')->nullable();
                $table->string('turno')->nullable();
                $table->string('tutor')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('espacios')) {
            Schema::connection('sqlite')->create('espacios', function (Blueprint $table): void {
                $table->string('aula')->primary();
                $table->string('descripcion')->nullable();
                $table->unsignedInteger('idDepartamento')->nullable();
            });
        }
    }
}
