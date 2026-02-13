<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Intranet\Http\Controllers\ComisionController;
use Intranet\Entities\Profesor;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ComisionControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'profesor']);

        $this->sqlitePath = storage_path('comision_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('comision_fcts');
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('centros');
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_paid_actualitza_estat_a_5(): void
    {
        $id = $this->seedComision(3);

        $controller = new DummyComisionController();
        $controller->paid($id);

        $this->assertSame(5, (int) DB::table('comisiones')->where('id', $id)->value('estado'));
    }

    public function test_unpaid_actualitza_estat_a_4_i_torna_back(): void
    {
        $id = $this->seedComision(3);
        $this->bindRequestWithReferer('/comision/' . $id . '/show');

        $controller = new DummyComisionController();
        $response = $controller->unpaid($id);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(4, (int) DB::table('comisiones')->where('id', $id)->value('estado'));
    }

    public function test_confirm_quan_estat_no_es_0_redirigix(): void
    {
        $id = $this->seedComision(2);

        $controller = new DummyComisionController();
        $response = $controller->confirm($id);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_createfct_fa_sync_sense_duplicar_i_guarda_aviso(): void
    {
        $comisionId = $this->seedComision(1);
        DB::table('fcts')->insert(['id' => 1001, 'idColaboracion' => 1, 'asociacion' => 1]);

        $controller = new DummyComisionController();
        $first = $controller->createFct(new Request([
            'idFct' => 1001,
            'hora_ini' => '09:00:00',
            'aviso' => 'on',
        ]), $comisionId);
        $second = $controller->createFct(new Request([
            'idFct' => 1001,
            'hora_ini' => '09:00:00',
            'aviso' => 'on',
        ]), $comisionId);

        $this->assertInstanceOf(Response::class, $first);
        $this->assertInstanceOf(Response::class, $second);
        $this->assertSame(1, DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1001)
            ->count());
        $this->assertSame(1, (int) DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1001)
            ->value('aviso'));
    }

    public function test_createfct_sense_aviso_guarda_0(): void
    {
        $comisionId = $this->seedComision(1);
        DB::table('fcts')->insert(['id' => 1002, 'idColaboracion' => 1, 'asociacion' => 1]);

        $controller = new DummyComisionController();
        $controller->createFct(new Request([
            'idFct' => 1002,
            'hora_ini' => '10:30:00',
        ]), $comisionId);

        $this->assertSame(0, (int) DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1002)
            ->value('aviso'));
    }

    public function test_createfct_llanca_excepcio_si_comissio_no_existix(): void
    {
        DB::table('fcts')->insert(['id' => 1010, 'idColaboracion' => 1, 'asociacion' => 1]);

        $controller = new DummyComisionController();

        $this->expectException(ModelNotFoundException::class);
        $controller->createFct(new Request([
            'idFct' => 1010,
            'hora_ini' => '09:00:00',
            'aviso' => 'on',
        ]), 999999);
    }

    public function test_deletefct_elimina_relacio_del_pivot(): void
    {
        $comisionId = $this->seedComision(1);
        DB::table('fcts')->insert(['id' => 1003, 'idColaboracion' => 1, 'asociacion' => 1]);
        DB::table('comision_fcts')->insert([
            'idComision' => $comisionId,
            'idFct' => 1003,
            'hora_ini' => '11:00:00',
            'aviso' => 1,
        ]);

        $controller = new DummyComisionController();
        $response = $controller->deleteFct($comisionId, 1003);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(0, DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1003)
            ->count());
    }

    public function test_detalle_deduplica_per_centre_i_ordena_select_per_nom(): void
    {
        $comisionId = $this->seedComision(1);

        DB::table('profesores')->insert([
            'dni' => 'P001',
            'nombre' => 'Prova',
            'apellido1' => 'Tutor',
            'apellido2' => 'Unit',
            'email' => 'p001@test.local',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
        ]);

        DB::table('centros')->insert([
            ['id' => 100, 'nombre' => 'Zeta Center'],
            ['id' => 200, 'nombre' => 'Alfa Center'],
        ]);

        DB::table('colaboraciones')->insert([
            ['id' => 1, 'idCentro' => 100],
            ['id' => 2, 'idCentro' => 100],
            ['id' => 3, 'idCentro' => 200],
        ]);

        DB::table('fcts')->insert([
            ['id' => 10, 'idColaboracion' => 1, 'asociacion' => 1, 'cotutor' => 'P001'],
            ['id' => 11, 'idColaboracion' => 2, 'asociacion' => 1, 'cotutor' => 'P001'],
            ['id' => 12, 'idColaboracion' => 3, 'asociacion' => 1, 'cotutor' => 'P001'],
            ['id' => 13, 'idColaboracion' => 3, 'asociacion' => 2, 'cotutor' => 'P001'], // no esFct
        ]);
        DB::table('alumno_fcts')->insert([
            ['id' => 1, 'idProfesor' => 'P001', 'idFct' => 10],
            ['id' => 2, 'idProfesor' => 'P001', 'idFct' => 11],
            ['id' => 3, 'idProfesor' => 'P001', 'idFct' => 12],
        ]);

        $usuario = Profesor::findOrFail('P001');
        $this->actingAs($usuario, 'profesor');

        $controller = new RealComisionController();
        $response = $controller->detalle($comisionId);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('comision.detalle', $response->name());

        $data = $response->getData();
        $this->assertArrayHasKey('allFcts', $data);
        $this->assertSame([
            12 => 'Alfa Center',
            11 => 'Zeta Center',
        ], $data['allFcts']);
    }

    private function seedComision(int $estado): int
    {
        return (int) DB::table('comisiones')->insertGetId([
            'idProfesor' => 'P001',
            'desde' => '2026-02-12 09:00:00',
            'hasta' => '2026-02-12 12:00:00',
            'fct' => 1,
            'servicio' => 'Visita d\'empresa',
            'kilometraje' => 0,
            'alojamiento' => 0,
            'comida' => 0,
            'gastos' => 0,
            'medio' => 0,
            'marca' => null,
            'matricula' => null,
            'itinerario' => null,
            'estado' => $estado,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('comisiones')) {
            Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10)->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->unsignedTinyInteger('fct')->default(0);
                $table->text('servicio')->nullable();
                $table->decimal('alojamiento', 8, 2)->default(0);
                $table->decimal('comida', 8, 2)->default(0);
                $table->decimal('gastos', 8, 2)->default(0);
                $table->unsignedInteger('kilometraje')->default(0);
                $table->unsignedTinyInteger('medio')->default(0);
                $table->string('marca')->nullable();
                $table->string('matricula')->nullable();
                $table->text('itinerario')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('fcts')) {
            Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('idColaboracion')->nullable();
                $table->string('cotutor', 10)->nullable();
                $table->unsignedTinyInteger('asociacion')->default(1);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('comision_fcts')) {
            Schema::connection('sqlite')->create('comision_fcts', function (Blueprint $table): void {
                $table->unsignedBigInteger('idComision');
                $table->unsignedBigInteger('idFct');
                $table->time('hora_ini')->nullable();
                $table->unsignedTinyInteger('aviso')->default(0);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->string('sustituye_a', 10)->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumno_fcts')) {
            Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedBigInteger('idFct')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('centros')) {
            Schema::connection('sqlite')->create('centros', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('colaboraciones')) {
            Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idCentro')->nullable();
            });
        }
    }

    private function bindRequestWithReferer(string $referer): void
    {
        $request = Request::create('/dummy', 'GET', [], [], [], ['HTTP_REFERER' => $referer]);
        $this->app->instance('request', $request);
    }
}

class DummyComisionController extends ComisionController
{
    public function __construct()
    {
        // Evitem inicialització de panel/UI en proves unitàries.
    }

    public function detalle($id)
    {
        return response('ok');
    }
}

class RealComisionController extends ComisionController
{
    public function __construct()
    {
        // Evitem inicialització de panel/UI en proves unitàries.
    }
}
