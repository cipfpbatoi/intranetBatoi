<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\ComisionController;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ComisionControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

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
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('comisiones');

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
