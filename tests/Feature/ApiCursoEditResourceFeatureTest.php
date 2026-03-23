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
 * Proves feature del payload `edit()` de cursos via JsonResource.
 */
class ApiCursoEditResourceFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_curso_edit_resource_testing.sqlite');
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
        $this->authenticateProfesor('PCR01');
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('cursos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_curso_edit_usa_resource_explicita(): void
    {
        DB::table('cursos')->insert([
            'id' => 51,
            'titulo' => 'Curs de prova',
            'tipo' => 2,
            'comentarios' => 'Comentari intern',
            'profesorado' => 'Claustre',
            'activo' => 1,
            'horas' => 25,
            'fecha_inicio' => '2026-04-02',
            'fecha_fin' => '2026-04-30',
            'hora_ini' => '08:30:00',
            'hora_fin' => '10:30:00',
            'aforo' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/curso/51/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.titulo', 'Curs de prova');
        $response->assertJsonPath('data.fecha_inicio', '02-04-2026');
        $response->assertJsonPath('data.fecha_fin', '30-04-2026');
        $response->assertJsonPath('data.hora_ini', '08:30');
        $response->assertJsonPath('data.hora_fin', '10:30');
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

        if (!Schema::connection('sqlite')->hasTable('cursos')) {
            Schema::connection('sqlite')->create('cursos', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('titulo')->nullable();
                $table->unsignedInteger('tipo')->nullable();
                $table->text('comentarios')->nullable();
                $table->text('profesorado')->nullable();
                $table->unsignedTinyInteger('activo')->default(1);
                $table->unsignedInteger('horas')->default(0);
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->time('hora_ini')->nullable();
                $table->time('hora_fin')->nullable();
                $table->unsignedInteger('aforo')->default(0);
                $table->timestamps();
            });
        }
    }

    private function authenticateProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => 1001,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => 'test@example.com',
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail($dni));
    }
}
