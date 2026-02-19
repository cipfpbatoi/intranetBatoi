<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\FaltaController;
use Tests\TestCase;

class FaltaControllerTest extends TestCase
{
    use WithoutModelEvents;

    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('falta_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('faltas');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_store_baja_crea_falta_en_estat_5_i_baixa_professor(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P900',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => null,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request([
            'idProfesor' => 'P900',
            'desde' => '2026-02-12',
            'motivos' => 1,
            'observaciones' => 'Baixa temporal',
            'baja' => 1,
        ]);

        $controller = new DummyFaltaController();
        $response = $controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);

        $falta = DB::table('faltas')->first();
        $this->assertNotNull($falta);
        $this->assertSame('P900', $falta->idProfesor);
        $this->assertSame(1, (int) $falta->baja);
        $this->assertSame(1, (int) $falta->dia_completo);
        $this->assertSame(5, (int) $falta->estado);
        $this->assertNull($falta->hora_ini);
        $this->assertNull($falta->hora_fin);

        $fechaBaja = (string) DB::table('profesores')->where('dni', 'P900')->value('fecha_baja');
        $this->assertStringStartsWith('2026-02-12', $fechaBaja);
    }

    public function test_alta_reactiva_professor_i_posa_estat_3(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P901',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => '2026-02-10',
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(Profesor::on('sqlite')->findOrFail('P901'), 'profesor');

        $faltaId = DB::table('faltas')->insertGetId([
            'idProfesor' => 'P901',
            'desde' => '2026-02-10',
            'hasta' => '2026-02-20',
            'motivos' => 1,
            'observaciones' => null,
            'baja' => 1,
            'dia_completo' => 1,
            'estado' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->bindRequestWithReferer('/falta/' . $faltaId . '/show');

        $controller = new DummyFaltaController();
        $response = $controller->alta($faltaId);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(3, (int) DB::table('faltas')->where('id', $faltaId)->value('estado'));
        $this->assertSame(0, (int) DB::table('faltas')->where('id', $faltaId)->value('baja'));
        $this->assertNull(DB::table('profesores')->where('dni', 'P901')->value('fecha_baja'));
    }

    public function test_init_posa_estat_1_si_no_hi_ha_fitxer_i_2_si_hi_ha(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P902',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => null,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(Profesor::on('sqlite')->findOrFail('P902'), 'profesor');

        $idSenseFitxer = DB::table('faltas')->insertGetId([
            'idProfesor' => 'P902',
            'desde' => '2026-02-10',
            'hasta' => '2026-02-10',
            'motivos' => 1,
            'baja' => 0,
            'dia_completo' => 1,
            'estado' => 0,
            'fichero' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idAmbFitxer = DB::table('faltas')->insertGetId([
            'idProfesor' => 'P902',
            'desde' => '2026-02-10',
            'hasta' => '2026-02-10',
            'motivos' => 1,
            'baja' => 0,
            'dia_completo' => 1,
            'estado' => 0,
            'fichero' => 'parts/falta.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $controller = new DummyFaltaController();
        $controller->init($idSenseFitxer);
        $controller->init($idAmbFitxer);

        $this->assertSame(1, (int) DB::table('faltas')->where('id', $idSenseFitxer)->value('estado'));
        $this->assertSame(2, (int) DB::table('faltas')->where('id', $idAmbFitxer)->value('estado'));
    }

    public function test_store_falla_si_no_hay_motivos(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P903',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => null,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request([
            'idProfesor' => 'P903',
            'desde' => '2026-02-12',
            'observaciones' => 'Sense motiu',
            'dia_completo' => 'on',
        ]);

        $controller = new DummyFaltaController();

        $this->expectException(ValidationException::class);
        $controller->store($request);
    }

    public function test_store_exige_horas_quan_no_es_dia_complet(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P904',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => null,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request([
            'idProfesor' => 'P904',
            'desde' => '2026-02-12',
            'hasta' => '2026-02-12',
            'motivos' => 1,
            'observaciones' => 'Sense hores',
        ]);

        $controller = new DummyFaltaController();

        try {
            $controller->store($request);
            $this->fail('S\'esperava ValidationException en faltar hores.');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $this->assertArrayHasKey('hora_ini', $errors);
            $this->assertArrayHasKey('hora_fin', $errors);
        }
    }

    public function test_update_no_exige_horas_quan_dia_completo_esta_marcat(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P905',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => null,
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $faltaId = DB::table('faltas')->insertGetId([
            'idProfesor' => 'P905',
            'desde' => '2026-02-10',
            'hasta' => '2026-02-10',
            'hora_ini' => '09:00:00',
            'hora_fin' => '10:00:00',
            'motivos' => 1,
            'baja' => 0,
            'dia_completo' => 0,
            'estado' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = new Request([
            'idProfesor' => 'P905',
            'desde' => '2026-02-10',
            'hasta' => '2026-02-10',
            'motivos' => 2,
            'observaciones' => 'Canviat a dia complet',
            'dia_completo' => 'on',
        ]);

        $controller = new DummyFaltaController();
        $response = $controller->update($request, $faltaId);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('rol')->default(3);
                $table->boolean('activo')->default(true);
                $table->date('fecha_baja')->nullable();
                $table->string('sustituye_a', 10)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('faltas')) {
            Schema::connection('sqlite')->create('faltas', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10);
                $table->boolean('baja')->default(false);
                $table->boolean('dia_completo')->default(true);
                $table->date('desde')->nullable();
                $table->date('hasta')->nullable();
                $table->time('hora_ini')->nullable();
                $table->time('hora_fin')->nullable();
                $table->unsignedInteger('motivos')->nullable();
                $table->string('observaciones', 200)->nullable();
                $table->string('fichero')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->timestamps();
            });
        }

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

        if (!Schema::connection('sqlite')->hasTable('notifications')) {
            Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->string('notifiable_id');
                $table->string('notifiable_type');
                $table->timestamps();
            });
        }
    }

    private function bindRequestWithReferer(string $referer): void
    {
        $request = Request::create('/dummy', 'GET', [], [], [], ['HTTP_REFERER' => $referer]);
        $this->app->instance('request', $request);
    }
}

class DummyFaltaController extends FaltaController
{
    public function __construct()
    {
        // Evitem inicialització de panell/UI en proves unitàries.
    }

    protected function redirect()
    {
        return redirect('/dummy');
    }
}
