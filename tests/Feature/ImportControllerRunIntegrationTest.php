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

    public function test_run_grups_converteix_tutor_en_blanc_a_sin_tutor(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <grupos>
    <grupo codigo="1CFSC" nombre="1R CFS ASIX -V- (LOE)" ensenanza="5" linea="1" turno="D" modalidad="COM" aula="T-117" capacidad="1" tutor_ppal=" " tutor_sec=" " oficial="S"/>
  </grupos>
</centro>
XML;

        $this->xmlPath = storage_path('import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(ImportController::class);
        $this->setCamposBdXml($controller, [
            $this->findCampoConfig($controller, 'Grupo', 'grupos'),
        ]);

        $request = Request::create('/import', 'POST', ['primera' => false]);
        $controller->run($this->xmlPath, $request);

        $grupo = DB::table('grupos')->where('codigo', '1CFSC')->first();
        $this->assertNotNull($grupo);
        $this->assertSame('SIN TUTOR', $grupo->tutor);
    }

    public function test_run_alumnos_filtra_estat_baixa(): void
    {
        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <alumnos>
    <alumno NIA="10861014" nombre="PAU" apellido1="FRAU" apellido2="MASANET" fecha_nac="30/01/2008" municipio_nac="9" municipio_nac_ext=" " provincia_nac="3" pais_nac="724" nacionalidad="724" sexo="H" tipo_doc="O" documento="029570816S" expediente=" " libro_escolaridad=" " cod_postal="03803" tipo_via="AV" domicilio="Avenida Alameda Camilo Sesto" numero="43B" puerta="izq" escalera=" " letra=" " piso="2" provincia="3" municipio="9" localidad="108525" telefono1="640355344" telefono2=" " telefono3=" " email1="fraumasanetpau45@gmail.com" email2=" " sip="7195255" nuss="031161419445" observaciones=" " ampa=" " seguro="N" fecha_matricula="29/07/2025" fecha_ingreso_centro="01/09/2025" estado_matricula="M" tipo_matricula="OR" repite="0" num_repeticion="0" ensenanza="5" curso="3306170449" grupo="1CFSC" turno="D" linea="1" trabaja="N" fuera_comunidad="N" matricula_parcial="N" matricula_condic="N" informe_medico="N" banco=" " sucursal=" " digito_control=" " cuenta=" " modalidad="COM" iban=" "/>
    <alumno NIA="10861015" nombre="BAIXA" apellido1="TEST" apellido2="B" fecha_nac="30/01/2008" municipio_nac="9" municipio_nac_ext=" " provincia_nac="3" pais_nac="724" nacionalidad="724" sexo="H" tipo_doc="O" documento="029570817S" expediente=" " libro_escolaridad=" " cod_postal="03803" tipo_via="AV" domicilio="Avenida Alameda Camilo Sesto" numero="43B" puerta="izq" escalera=" " letra=" " piso="2" provincia="3" municipio="9" localidad="108525" telefono1="640355344" telefono2=" " telefono3=" " email1="baixa@example.com" email2=" " sip="7195255" nuss="031161419445" observaciones=" " ampa=" " seguro="N" fecha_matricula="29/07/2025" fecha_ingreso_centro="01/09/2025" estado_matricula="B" tipo_matricula="OR" repite="0" num_repeticion="0" ensenanza="5" curso="3306170449" grupo="1CFSC" turno="D" linea="1" trabaja="N" fuera_comunidad="N" matricula_parcial="N" matricula_condic="N" informe_medico="N" banco=" " sucursal=" " digito_control=" " cuenta=" " modalidad="COM" iban=" "/>
  </alumnos>
</centro>
XML;

        $this->xmlPath = storage_path('import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(ImportController::class);
        $this->setCamposBdXml($controller, [
            $this->findCampoConfig($controller, 'Alumno', 'alumnos'),
        ]);

        $request = Request::create('/import', 'POST', ['primera' => false]);
        $controller->run($this->xmlPath, $request);

        $this->assertNotNull(DB::table('alumnos')->where('nia', '10861014')->first());
        $this->assertNull(DB::table('alumnos')->where('nia', '10861015')->first());
    }

    public function test_run_horaris_ocupacions_respecta_plantilla_mes_alta(): void
    {
        DB::table('horarios')->insert([
            'idProfesor' => 'OLD',
            'dia_semana' => 'L',
            'sesion_orden' => 1,
            'plantilla' => 20,
            'ocupacion' => 'OLD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <horarios_ocupaciones>
    <horario_ocupacion dia_semana="L" sesion_orden="2" plantilla="10" docente="PROF1" ocupacion="GUARDIA"/>
    <horario_ocupacion dia_semana="L" sesion_orden="3" plantilla="30" docente="PROF1" ocupacion="GUARDIA30"/>
  </horarios_ocupaciones>
</centro>
XML;

        $this->xmlPath = storage_path('import_run_integration.xml');
        file_put_contents($this->xmlPath, $xml);

        $controller = app(ImportController::class);
        $this->setCamposBdXml($controller, [
            $this->findCampoConfig($controller, 'Horario', 'horarios_ocupaciones'),
        ]);

        $request = Request::create('/import', 'POST', ['primera' => false]);
        $controller->run($this->xmlPath, $request);

        $horaris = DB::table('horarios')->get();
        $this->assertCount(1, $horaris);
        $this->assertSame(30, (int) $horaris[0]->plantilla);
        $this->assertSame('GUARDIA30', $horaris[0]->ocupacion);
    }

    /**
     * @return array<string, mixed>
     */
    private function findCampoConfig(ImportController $controller, string $className, string $xmlName): array
    {
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('camposBdXml');
        $method->setAccessible(true);
        /** @var array<int, array<string, mixed>> $configs */
        $configs = $method->invoke($controller);

        foreach ($configs as $config) {
            if (($config['nombreclase'] ?? null) === $className && ($config['nombrexml'] ?? null) === $xmlName) {
                return $config;
            }
        }

        throw new \RuntimeException("Config no trobada per {$className}/{$xmlName}");
    }

    /**
     * @param array<int, array<string, mixed>> $configs
     */
    private function setCamposBdXml(ImportController $controller, array $configs): void
    {
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('camposBdXml');
        $property->setAccessible(true);
        $property->setValue($controller, $configs);
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

        if (!Schema::connection('sqlite')->hasTable('alumnos')) {
            Schema::connection('sqlite')->create('alumnos', function (Blueprint $table): void {
                $table->string('nia')->primary();
                $table->string('dni')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->date('fecha_nac')->nullable();
                $table->string('sexo')->nullable();
                $table->string('expediente')->nullable();
                $table->string('domicilio')->nullable();
                $table->string('codigo_postal')->nullable();
                $table->string('provincia')->nullable();
                $table->string('municipio')->nullable();
                $table->date('fecha_ingreso')->nullable();
                $table->date('fecha_matricula')->nullable();
                $table->string('repite')->nullable();
                $table->string('turno')->nullable();
                $table->string('trabaja')->nullable();
                $table->string('password')->nullable();
                $table->date('baja')->nullable();
                $table->string('email')->nullable();
                $table->string('telef1')->nullable();
                $table->string('telef2')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumnos_grupos')) {
            Schema::connection('sqlite')->create('alumnos_grupos', function (Blueprint $table): void {
                $table->string('idAlumno');
                $table->string('idGrupo');
                $table->string('subGrupo')->nullable();
                $table->string('posicion')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('horarios')) {
            Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor')->nullable();
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
