<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Mockery;
use Tests\TestCase;

class ApiAlumnoGrupoControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_alumno_grupo_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('alumnos_grupos');
        Schema::connection('sqlite')->dropIfExists('alumnos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        Mockery::close();
        parent::tearDown();
    }

    public function test_show_per_tutor_torna_alumnat_ordenat(): void
    {
        $this->insertProfesor('PAG01');
        $user = Profesor::on('sqlite')->findOrFail('PAG01');
        $this->actingAs($user, 'api');

        DB::table('alumnos')->insert([
            ['nia' => '20000001', 'nombre' => 'Joan', 'apellido1' => 'Zulu', 'apellido2' => 'A'],
            ['nia' => '20000002', 'nombre' => 'Anna', 'apellido1' => 'Alpha', 'apellido2' => 'B'],
        ]);
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => '20000001', 'idGrupo' => 'G1'],
            ['idAlumno' => '20000002', 'idGrupo' => 'G1'],
        ]);

        $mockService = Mockery::mock(GrupoService::class);
        $grupo = new Grupo();
        $grupo->codigo = 'G1';
        $mockService->shouldReceive('qTutor')
            ->once()
            ->with('PAG01')
            ->andReturn(new EloquentCollection([$grupo]));
        $this->app->instance(GrupoService::class, $mockService);

        $response = $this->getJson('/api/alumnogrupo/PAG01');

        $response->assertOk();
        $response->assertJsonPath('0.id', 20000002);
        $response->assertJsonPath('0.name', 'Alpha B, Anna');
        $response->assertJsonPath('1.id', 20000001);
        $response->assertJsonPath('1.name', 'Zulu A, Joan');
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->integer('codigo')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->string('api_token', 80)->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumnos')) {
            Schema::connection('sqlite')->create('alumnos', function (Blueprint $table): void {
                $table->string('nia', 20)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumnos_grupos')) {
            Schema::connection('sqlite')->create('alumnos_grupos', function (Blueprint $table): void {
                $table->string('idAlumno', 20);
                $table->string('idGrupo', 20);
                $table->string('subGrupo', 1)->nullable();
                $table->unsignedInteger('posicion')->nullable();
            });
        }
    }

    private function insertProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
