<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Proves feature d'AlumnoReunion amb autenticació Sanctum.
 */
class ApiAlumnoReunionControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_alumno_reunion_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('alumno_reuniones');
        Schema::connection('sqlite')->dropIfExists('reuniones');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_resumen_retorna_counts_i_exclou_semipresencial(): void
    {
        $this->insertProfesor('PRU01');
        $user = Profesor::on('sqlite')->findOrFail('PRU01');
        Sanctum::actingAs($user);

        $cursoAcademico = curso();

        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'turno' => 'M', 'curso' => 1],
            ['codigo' => 'G2', 'turno' => 'V', 'curso' => 2],
            ['codigo' => 'GS', 'turno' => 'S', 'curso' => 1],
        ]);

        DB::table('reuniones')->insert([
            ['id' => 1, 'idGrupo' => 'G1', 'curso' => $cursoAcademico],
            ['id' => 2, 'idGrupo' => 'G2', 'curso' => $cursoAcademico],
            ['id' => 3, 'idGrupo' => 'GS', 'curso' => $cursoAcademico],
        ]);

        DB::table('alumno_reuniones')->insert([
            ['idReunion' => 1, 'idAlumno' => '20000001', 'capacitats' => 1, 'sent' => 1, 'token' => 'tok-1'],
            ['idReunion' => 1, 'idAlumno' => '20000002', 'capacitats' => 3, 'sent' => 1, 'token' => 'tok-2'],
            ['idReunion' => 2, 'idAlumno' => '20000003', 'capacitats' => 1, 'sent' => 0, 'token' => null],
            ['idReunion' => 2, 'idAlumno' => '20000004', 'capacitats' => 3, 'sent' => 1, 'token' => 'tok-3'],
            ['idReunion' => 3, 'idAlumno' => '20000005', 'capacitats' => 1, 'sent' => 1, 'token' => 'tok-semi-1'],
            ['idReunion' => 3, 'idAlumno' => '20000006', 'capacitats' => 3, 'sent' => 1, 'token' => 'tok-semi-2'],
        ]);

        $response = $this->getJson('/api/alumnoreunion/resumen');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.curso', $cursoAcademico);
        $response->assertJsonPath('data.exclou_semipresencial', true);
        $response->assertJsonPath('data.curs_1.promocionen', 1);
        $response->assertJsonPath('data.curs_1.repeteixen', 1);
        $response->assertJsonPath('data.curs_2.promocionen', 1);
        $response->assertJsonPath('data.curs_2.repeteixen', 1);
        $response->assertJsonPath('data.tokens_generats', 3);
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

        if (!Schema::connection('sqlite')->hasTable('grupos')) {
            Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
                $table->string('codigo', 20)->primary();
                $table->string('nombre')->nullable();
                $table->string('turno', 1)->nullable();
                $table->unsignedTinyInteger('curso')->nullable();
                $table->string('tutor', 10)->nullable();
                $table->unsignedInteger('idCiclo')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('reuniones')) {
            Schema::connection('sqlite')->create('reuniones', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idGrupo', 20)->nullable();
                $table->string('curso', 20)->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumno_reuniones')) {
            Schema::connection('sqlite')->create('alumno_reuniones', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idReunion');
                $table->string('idAlumno', 20);
                $table->unsignedTinyInteger('capacitats')->default(0);
                $table->boolean('sent')->default(false);
                $table->string('token', 60)->nullable();
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
