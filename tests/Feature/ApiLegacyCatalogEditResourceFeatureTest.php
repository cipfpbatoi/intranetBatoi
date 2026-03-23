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
 * Proves feature de payloads `edit()` per a catàlegs legacy simples.
 */
class ApiLegacyCatalogEditResourceFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_legacy_catalog_edit_resource_testing.sqlite');
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
        $this->authenticateProfesor('PLC01');
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('tipo_actividad');
        Schema::connection('sqlite')->dropIfExists('tipoincidencias');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_tipoactividad_edit_usa_resource_explicita(): void
    {
        DB::table('tipo_actividad')->insert([
            'id' => 7,
            'cliteral' => 'VISITA',
            'vliteral' => 'Visita',
            'departamento_id' => null,
            'justificacio' => 'Text justificacio',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/tipoactividad/7/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 7);
        $response->assertJsonPath('data.cliteral', 'VISITA');
        $response->assertJsonPath('data.vliteral', 'Visita');
        $response->assertJsonPath('data.justificacio', 'Text justificacio');
    }

    public function test_tipoincidencia_edit_usa_resource_explicita(): void
    {
        DB::table('tipoincidencias')->insert([
            'id' => 12,
            'nombre' => 'Averia',
            'nom' => 'Avaria',
            'idProfesor' => 'PLC01',
            'tipus' => 2,
        ]);

        $response = $this->getJson('/api/tipoincidencia/12/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 12);
        $response->assertJsonPath('data.nombre', 'Averia');
        $response->assertJsonPath('data.nom', 'Avaria');
        $response->assertJsonPath('data.idProfesor', 'PLC01');
        $response->assertJsonPath('data.tipus', 2);
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

        if (!Schema::connection('sqlite')->hasTable('tipo_actividad')) {
            Schema::connection('sqlite')->create('tipo_actividad', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('cliteral')->nullable();
                $table->string('vliteral')->nullable();
                $table->unsignedInteger('departamento_id')->nullable();
                $table->text('justificacio')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('tipoincidencias')) {
            Schema::connection('sqlite')->create('tipoincidencias', function (Blueprint $table): void {
                $table->integer('id')->primary();
                $table->string('nombre')->nullable();
                $table->string('nom')->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedInteger('tipus')->nullable();
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
