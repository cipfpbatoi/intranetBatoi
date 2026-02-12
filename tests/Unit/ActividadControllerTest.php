<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\ActividadController;
use Styde\Html\Facades\Alert;
use Tests\TestCase;

class ActividadControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('actividad_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('autorizaciones');
        Schema::connection('sqlite')->dropIfExists('actividad_grupo');
        Schema::connection('sqlite')->dropIfExists('actividad_profesor');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('alumnos');
        Schema::connection('sqlite')->dropIfExists('profesores');
        Schema::connection('sqlite')->dropIfExists('actividades');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_notify_torna_back_si_no_hi_ha_coordinador(): void
    {
        $actividad = ActividadLite::create([
            'name' => 'Excursio',
        ]);

        $this->bindRequestWithReferer('/activitat/' . $actividad->id);
        Alert::shouldReceive('warning')->once();

        $controller = new DummyActividadController();
        $response = $controller->notify($actividad->id);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_menorauth_torna_back_si_alumne_no_esta_assocciat(): void
    {
        $actividad = ActividadLite::create([
            'name' => 'Eixida',
        ]);
        AlumnoLite::create([
            'nia' => 'A0001',
        ]);

        $this->bindRequestWithReferer('/activitat/' . $actividad->id . '/autoritzats');
        Alert::shouldReceive('warning')->once();

        $controller = new DummyActividadController();
        $response = $controller->menorAuth('A0001', $actividad->id);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_detalle_filtra_selects_sense_elements_ja_assignats(): void
    {
        $actividad = ActividadLite::create([
            'name' => 'Jornada',
        ]);

        DB::table('profesores')->insert([
            [
                'dni' => 'P001',
                'nombre' => 'Ana',
                'apellido1' => 'Alfa',
                'apellido2' => 'A',
                'fecha_baja' => null,
                'activo' => 1,
            ],
            [
                'dni' => 'P002',
                'nombre' => 'Bernat',
                'apellido1' => 'Beta',
                'apellido2' => 'B',
                'fecha_baja' => null,
                'activo' => 1,
            ],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup 1'],
            ['codigo' => 'G2', 'nombre' => 'Grup 2'],
        ]);

        DB::table('actividad_profesor')->insert([
            ['idActividad' => $actividad->id, 'idProfesor' => 'P001', 'coordinador' => 1],
        ]);

        DB::table('actividad_grupo')->insert([
            ['idActividad' => $actividad->id, 'idGrupo' => 'G1'],
        ]);

        $controller = new DummyActividadController();
        $view = $controller->detalle($actividad->id);

        $this->assertSame('extraescolares.edit', $view->name());
        $data = $view->getData();

        $this->assertArrayHasKey('tProfesores', $data);
        $this->assertArrayHasKey('tGrupos', $data);
        $this->assertArrayNotHasKey('P001', $data['tProfesores']);
        $this->assertArrayHasKey('P002', $data['tProfesores']);
        $this->assertArrayNotHasKey('G1', $data['tGrupos']);
        $this->assertArrayHasKey('G2', $data['tGrupos']);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('actividades')) {
            Schema::connection('sqlite')->create('actividades', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->tinyInteger('extraescolar')->default(1);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table) {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('grupos')) {
            Schema::connection('sqlite')->create('grupos', function (Blueprint $table) {
                $table->string('codigo', 5)->primary();
                $table->string('nombre')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('actividad_profesor')) {
            Schema::connection('sqlite')->create('actividad_profesor', function (Blueprint $table) {
                $table->unsignedBigInteger('idActividad');
                $table->string('idProfesor', 10);
                $table->boolean('coordinador')->default(false);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('actividad_grupo')) {
            Schema::connection('sqlite')->create('actividad_grupo', function (Blueprint $table) {
                $table->unsignedBigInteger('idActividad');
                $table->string('idGrupo', 5);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumnos')) {
            Schema::connection('sqlite')->create('alumnos', function (Blueprint $table) {
                $table->string('nia', 10)->primary();
                $table->string('nombre')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('autorizaciones')) {
            Schema::connection('sqlite')->create('autorizaciones', function (Blueprint $table) {
                $table->unsignedBigInteger('idActividad');
                $table->string('idAlumno', 10);
                $table->boolean('autorizado')->default(false);
            });
        }
    }

    private function bindRequestWithReferer(string $referer): void
    {
        $request = Request::create('/dummy', 'GET', [], [], [], ['HTTP_REFERER' => $referer]);
        $this->app->instance('request', $request);
    }
}

class ActividadLite extends Model
{
    protected $table = 'actividades';
    public $timestamps = false;
    protected $guarded = [];
}

class AlumnoLite extends Model
{
    protected $table = 'alumnos';
    protected $primaryKey = 'nia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}

class DummyActividadController extends ActividadController
{
    public function __construct()
    {
        // Evitem inicialització de UI/panell en proves unitàries.
    }
}
