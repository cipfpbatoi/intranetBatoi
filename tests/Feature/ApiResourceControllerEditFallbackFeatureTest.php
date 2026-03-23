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
 * Proves feature del fallback de `edit()` en ApiResourceController.
 */
class ApiResourceControllerEditFallbackFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_resource_edit_fallback_testing.sqlite');
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
        $this->authenticateProfesor('PRF01');
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('actividades');
        Schema::connection('sqlite')->dropIfExists('tipo_expedientes');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_edit_sense_editresource_continua_retorna_model_serialitzat(): void
    {
        DB::table('tipo_expedientes')->insert([
            'id' => 31,
            'titulo' => 'Amonestacio',
            'rol' => 3,
            'informe' => 0,
            'orientacion' => null,
        ]);

        $response = $this->getJson('/api/tipoExpediente/31/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 31);
        $response->assertJsonPath('data.titulo', 'Amonestacio');
        $response->assertJsonPath('data.rol', 3);
    }

    public function test_edit_amb_resource_explicita_retorna_404_quan_no_existeix(): void
    {
        $response = $this->getJson('/api/actividad/999999/edit');

        $response->assertNotFound();
        $response->assertJsonPath('success', false);
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

        if (!Schema::connection('sqlite')->hasTable('tipo_expedientes')) {
            Schema::connection('sqlite')->create('tipo_expedientes', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('titulo')->nullable();
                $table->unsignedInteger('rol')->default(0);
                $table->unsignedTinyInteger('informe')->default(0);
                $table->string('orientacion')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('actividades')) {
            Schema::connection('sqlite')->create('actividades', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
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
