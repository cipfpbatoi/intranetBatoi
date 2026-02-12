<?php

namespace Tests\Unit\Services;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Services\School\ExpedienteWorkflowService;
use Mockery;
use Tests\TestCase;

class ExpedienteWorkflowServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();

        $notificationService = Mockery::mock(NotificationService::class);
        $notificationService->shouldReceive('send')->andReturnNull();
        $this->app->instance(NotificationService::class, $notificationService);
    }

    public function test_authorize_pending_actualitza_sols_estat_1(): void
    {
        $id1 = $this->insertExpediente(1, 1, 'AL1', 'PR1');
        $id2 = $this->insertExpediente(3, 1, 'AL1', 'PR1');

        (new ExpedienteWorkflowService())->authorizePending();

        $this->assertSame(2, (int) DB::table('expedientes')->where('id', $id1)->value('estado'));
        $this->assertSame(3, (int) DB::table('expedientes')->where('id', $id2)->value('estado'));
    }

    public function test_init_passa_a_1_quan_no_es_orientacio(): void
    {
        $id = $this->insertExpediente(0, 1, 'AL1', 'PR1');

        $result = (new ExpedienteWorkflowService())->init($id);

        $this->assertTrue($result);
        $this->assertSame(1, (int) DB::table('expedientes')->where('id', $id)->value('estado'));
    }

    public function test_init_passa_a_4_quan_es_orientacio(): void
    {
        $id = $this->insertExpediente(0, 2, 'AL1', 'PR1');

        $result = (new ExpedienteWorkflowService())->init($id);

        $this->assertTrue($result);
        $this->assertSame(4, (int) DB::table('expedientes')->where('id', $id)->value('estado'));
    }

    public function test_pass_to_orientation_assigna_estat_i_data(): void
    {
        $id = $this->insertExpediente(4, 2, 'AL1', 'PR1');

        $result = (new ExpedienteWorkflowService())->passToOrientation($id);

        $this->assertTrue($result);
        $this->assertSame(5, (int) DB::table('expedientes')->where('id', $id)->value('estado'));
        $this->assertNotNull(DB::table('expedientes')->where('id', $id)->value('fechasolucion'));
    }

    public function test_assign_companion_actualitza_acompanyant_i_estat(): void
    {
        $id = $this->insertExpediente(4, 2, 'AL1', 'PR1');

        $result = (new ExpedienteWorkflowService())->assignCompanion($id, 'PR2');

        $this->assertTrue($result);
        $this->assertSame('PR2', DB::table('expedientes')->where('id', $id)->value('idAcompanyant'));
        $this->assertSame(5, (int) DB::table('expedientes')->where('id', $id)->value('estado'));
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->date('fecha_baja')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->date('fecha_nac')->nullable();
            $table->timestamps();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->timestamps();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno');
            $table->string('idGrupo');
            $table->string('subGrupo')->nullable();
            $table->string('posicion')->nullable();
        });

        $schema->create('tipo_expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('titulo')->nullable();
            $table->unsignedTinyInteger('orientacion')->default(0);
            $table->unsignedTinyInteger('informe')->default(0);
            $table->unsignedInteger('rol')->default(1);
        });

        $schema->create('expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('tipo')->nullable();
            $table->string('idModulo')->nullable();
            $table->string('idAlumno')->nullable();
            $table->string('idProfesor')->nullable();
            $table->string('idAcompanyant')->nullable();
            $table->text('explicacion')->nullable();
            $table->date('fecha')->nullable();
            $table->date('fechatramite')->nullable();
            $table->date('fechasolucion')->nullable();
            $table->tinyInteger('estado')->default(0);
        });

        DB::table('profesores')->insert([
            ['dni' => 'PR1', 'nombre' => 'Tutor', 'apellido1' => 'A', 'apellido2' => 'B', 'rol' => 3, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'PR2', 'nombre' => 'Acomp', 'apellido1' => 'C', 'apellido2' => 'D', 'rol' => 3, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('alumnos')->insert([
            'nia' => 'AL1',
            'nombre' => 'Alumne',
            'apellido1' => 'Un',
            'apellido2' => 'Dos',
            'fecha_nac' => '2010-01-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => '1A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('alumnos_grupos')->insert([
            'idAlumno' => 'AL1',
            'idGrupo' => 'G1',
            'subGrupo' => 'A',
            'posicion' => '1',
        ]);

        DB::table('tipo_expedientes')->insert([
            ['id' => 1, 'titulo' => 'Normal', 'orientacion' => 0, 'informe' => 0, 'rol' => 1],
            ['id' => 2, 'titulo' => 'Orientacio', 'orientacion' => 1, 'informe' => 0, 'rol' => 1],
        ]);
    }

    private function insertExpediente(int $estado, int $tipo, string $idAlumno, string $idProfesor): int
    {
        return (int) DB::table('expedientes')->insertGetId([
            'tipo' => $tipo,
            'idAlumno' => $idAlumno,
            'idProfesor' => $idProfesor,
            'explicacion' => 'Explicacio test',
            'fecha' => '2026-02-12',
            'estado' => $estado,
        ]);
    }
}

