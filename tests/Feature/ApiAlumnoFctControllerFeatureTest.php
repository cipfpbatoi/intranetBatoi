<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiAlumnoFctControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_alumnofct_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_update_canvia_pg0301_i_a56(): void
    {
        $this->insertProfesor('PAF01');
        $user = Profesor::on('sqlite')->findOrFail('PAF01');
        $this->actingAs($user, 'api');

        DB::table('alumno_fcts')->insert([
            'id' => 101,
            'idFct' => 1,
            'idAlumno' => '10802710',
            'idProfesor' => 'PAF01',
            'horas' => 380,
            'pg0301' => 0,
            'a56' => 0,
        ]);

        $response = $this->putJson('/api/alumnofct/101', [
            'pg0301' => 'true',
            'a56' => 'true',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.updated', true);

        $this->assertSame(1, (int) DB::table('alumno_fcts')->where('id', 101)->value('pg0301'));
        $this->assertSame(1, (int) DB::table('alumno_fcts')->where('id', 101)->value('a56'));
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

        if (!Schema::connection('sqlite')->hasTable('alumno_fcts')) {
            Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idFct')->nullable();
                $table->string('idAlumno', 20)->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedInteger('horas')->default(0);
                $table->unsignedTinyInteger('pg0301')->default(0);
                $table->unsignedTinyInteger('a56')->default(0);
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

