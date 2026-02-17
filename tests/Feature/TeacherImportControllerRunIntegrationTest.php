<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\TeacherImportController;
use Tests\TestCase;

class TeacherImportControllerRunIntegrationTest extends TestCase
{
    private string $sqlitePath;
    private string $xmlPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('teacher_import_run_integration_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('profesores');
        Schema::connection('sqlite')->dropIfExists('horarios');

        if (isset($this->xmlPath) && file_exists($this->xmlPath)) {
            @unlink($this->xmlPath);
        }
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_run_importa_docent_quan_id_profesor_coincidix(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes>
    <docente documento="021648508B" nombre="PROVA" apellido1="DOCENT" apellido2="TEST" sexo="H" cod_postal="03803" domicilio="Carrer prova" telefono1="600000000" telefono2=" " email1="docent@test.local" titular_sustituido=" " fecha_nac="01/01/1980" fecha_ingreso="01/09/2010" fecha_antiguedad="01/09/2010"/>
  </docentes>
  <horarios_grupo/>
  <horarios_ocupaciones/>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '021648508B',
            'horari' => false,
            'lost' => false,
        ]);

        $controller->run($this->xmlPath, $request);

        $profesor = DB::table('profesores')->where('dni', '021648508B')->first();
        $this->assertNotNull($profesor);
        $this->assertSame('PROVA', $profesor->nombre);
        $this->assertSame('DOCENT', $profesor->apellido1);
        $this->assertSame('TEST', $profesor->apellido2);
        $this->assertSame('03803', $profesor->codigo_postal);
        $this->assertSame('docent@test.local', $profesor->emailItaca);
        $this->assertSame(1, (int) $profesor->activo);
    }

    public function test_run_no_importa_docent_quan_id_profesor_no_coincidix_i_no_existix(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes>
    <docente documento="021648508B" nombre="PROVA" apellido1="DOCENT" apellido2="TEST" sexo="H" cod_postal="03803" domicilio="Carrer prova" telefono1="600000000" telefono2=" " email1="docent@test.local" titular_sustituido=" " fecha_nac="01/01/1980" fecha_ingreso="01/09/2010" fecha_antiguedad="01/09/2010"/>
  </docentes>
  <horarios_grupo/>
  <horarios_ocupaciones/>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '000000000X',
            'horari' => false,
            'lost' => false,
        ]);

        $controller->run($this->xmlPath, $request);

        $profesor = DB::table('profesores')->where('dni', '021648508B')->first();
        $this->assertNull($profesor);
    }

    public function test_run_actualitza_docent_existent_encara_que_id_profesor_no_coincidix(): void
    {
        DB::table('profesores')->insert([
            'dni' => '021648508B',
            'nombre' => 'ABANS',
            'apellido1' => 'ABANS1',
            'apellido2' => 'ABANS2',
            'codigo_postal' => '00000',
            'emailItaca' => 'abans@test.local',
            'activo' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes>
    <docente documento="021648508B" nombre="NOU" apellido1="DOCENT" apellido2="ACTUALITZAT" sexo="H" cod_postal="03803" domicilio="Carrer prova" telefono1="600000000" telefono2=" " email1="nou@test.local" titular_sustituido=" " fecha_nac="01/01/1980" fecha_ingreso="01/09/2010" fecha_antiguedad="01/09/2010"/>
  </docentes>
  <horarios_grupo/>
  <horarios_ocupaciones/>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '000000000X',
            'horari' => false,
            'lost' => false,
        ]);

        $controller->run($this->xmlPath, $request);

        $profesor = DB::table('profesores')->where('dni', '021648508B')->first();
        $this->assertNotNull($profesor);
        $this->assertSame('NOU', $profesor->nombre);
        $this->assertSame('ACTUALITZAT', $profesor->apellido2);
        $this->assertSame('03803', $profesor->codigo_postal);
        $this->assertSame('nou@test.local', $profesor->emailItaca);
        $this->assertSame(1, (int) $profesor->activo);
    }

    public function test_run_importa_horaris_quan_id_profesor_coincidix(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes/>
  <horarios_grupo>
    <horario_grupo dia_semana="L" sesion_orden="1" plantilla="10" docente="021648508B" contenido="M01" grupo="1CFSC" aula="A-213"/>
  </horarios_grupo>
  <horarios_ocupaciones>
    <horario_ocupacion dia_semana="L" sesion_orden="2" plantilla="10" docente="021648508B" ocupacion="GUARDIA"/>
  </horarios_ocupaciones>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '021648508B',
            'horari' => false,
            'lost' => false,
        ]);

        $controller->run($this->xmlPath, $request);

        $horaris = DB::table('horarios')->where('idProfesor', '021648508B')->orderBy('sesion_orden')->get();
        $this->assertCount(2, $horaris);
        $this->assertSame('M01', $horaris[0]->modulo);
        $this->assertSame('1CFSC', $horaris[0]->idGrupo);
        $this->assertSame('A-213', $horaris[0]->aula);
        $this->assertSame('GUARDIA', $horaris[1]->ocupacion);
    }

    public function test_run_no_importa_horaris_quan_id_profesor_no_coincidix(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes/>
  <horarios_grupo>
    <horario_grupo dia_semana="L" sesion_orden="1" plantilla="10" docente="021648508B" contenido="M01" grupo="1CFSC" aula="A-213"/>
  </horarios_grupo>
  <horarios_ocupaciones>
    <horario_ocupacion dia_semana="L" sesion_orden="2" plantilla="10" docente="021648508B" ocupacion="GUARDIA"/>
  </horarios_ocupaciones>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '000000000X',
            'horari' => false,
            'lost' => false,
        ]);

        $controller->run($this->xmlPath, $request);

        $this->assertSame(0, DB::table('horarios')->count());
    }

    public function test_run_horari_true_lost_false_usa_plantilla_global_i_pot_bloquejar_import(): void
    {
        DB::table('horarios')->insert([
            [
                'idProfesor' => '021648508B',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'plantilla' => 5,
                'modulo' => 'OLD1',
                'idGrupo' => 'G1',
                'aula' => 'A-101',
                'ocupacion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '99999999Z',
                'dia_semana' => 'L',
                'sesion_orden' => 2,
                'plantilla' => 20,
                'modulo' => 'OLD2',
                'idGrupo' => 'G2',
                'aula' => 'A-102',
                'ocupacion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes/>
  <horarios_grupo>
    <horario_grupo dia_semana="L" sesion_orden="1" plantilla="10" docente="021648508B" contenido="M01" grupo="1CFSC" aula="A-213"/>
  </horarios_grupo>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '021648508B',
            'horari' => true,
            'lost' => false,
        ]);

        $controller->run($this->xmlPath, $request);

        $this->assertSame(0, DB::table('horarios')->where('idProfesor', '021648508B')->count());
        $this->assertSame(1, DB::table('horarios')->where('idProfesor', '99999999Z')->count());
    }

    public function test_run_horari_true_lost_true_usa_plantilla_del_professor_i_permet_import(): void
    {
        DB::table('horarios')->insert([
            [
                'idProfesor' => '021648508B',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'plantilla' => 5,
                'modulo' => 'OLD1',
                'idGrupo' => 'G1',
                'aula' => 'A-101',
                'ocupacion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => '99999999Z',
                'dia_semana' => 'L',
                'sesion_orden' => 2,
                'plantilla' => 20,
                'modulo' => 'OLD2',
                'idGrupo' => 'G2',
                'aula' => 'A-102',
                'ocupacion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes/>
  <horarios_grupo>
    <horario_grupo dia_semana="L" sesion_orden="1" plantilla="10" docente="021648508B" contenido="M01" grupo="1CFSC" aula="A-213"/>
  </horarios_grupo>
</centro>
XML;

        $this->xmlPath = storage_path('teacher_import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(TeacherImportController::class);
        $request = Request::create('/teacherImport', 'POST', [
            'idProfesor' => '021648508B',
            'horari' => true,
            'lost' => true,
        ]);

        $controller->run($this->xmlPath, $request);

        $importat = DB::table('horarios')->where('idProfesor', '021648508B')->first();
        $this->assertNotNull($importat);
        $this->assertSame(10, (int) $importat->plantilla);
        $this->assertSame('M01', $importat->modulo);
        $this->assertSame(1, DB::table('horarios')->where('idProfesor', '99999999Z')->count());
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('codigo')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('sexo')->nullable();
                $table->string('codigo_postal')->nullable();
                $table->string('domicilio')->nullable();
                $table->string('movil1')->nullable();
                $table->string('movil2')->nullable();
                $table->string('emailItaca')->nullable();
                $table->string('sustituye_a')->nullable();
                $table->date('fecha_nac')->nullable();
                $table->date('fecha_ingreso')->nullable();
                $table->date('fecha_ant')->nullable();
                $table->boolean('activo')->default(true);
                $table->string('email')->nullable();
                $table->unsignedInteger('departamento')->nullable();
                $table->string('password')->nullable();
                $table->string('api_token')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('horarios')) {
            Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10)->nullable();
                $table->string('dia_semana')->nullable();
                $table->unsignedInteger('sesion_orden')->nullable();
                $table->unsignedInteger('plantilla')->nullable();
                $table->string('modulo')->nullable();
                $table->string('idGrupo')->nullable();
                $table->string('aula')->nullable();
                $table->string('ocupacion')->nullable();
                $table->timestamps();
            });
        }
    }
}
