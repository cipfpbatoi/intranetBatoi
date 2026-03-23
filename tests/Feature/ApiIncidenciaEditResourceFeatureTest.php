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
 * Proves feature del payload `edit()` d'incidències via JsonResource.
 */
class ApiIncidenciaEditResourceFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_incidencia_edit_resource_testing.sqlite');
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
        $this->authenticateProfesor('PIN01');
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('incidencias');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_incidencia_edit_usa_resource_explicita(): void
    {
        DB::table('incidencias')->insert([
            'id' => 1385,
            'tipo' => 8,
            'espacio' => 'A101',
            'material' => 'MAT-01',
            'descripcion' => 'No funciona el projector',
            'imagen' => 'incidencias/prova.jpg',
            'idProfesor' => 'PIN01',
            'prioridad' => 2,
            'observaciones' => 'Nomes a primera hora',
            'fecha' => '2026-03-23',
        ]);

        $response = $this->getJson('/api/incidencia/1385/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.tipo', 8);
        $response->assertJsonPath('data.espacio', 'A101');
        $response->assertJsonPath('data.material', 'MAT-01');
        $response->assertJsonPath('data.descripcion', 'No funciona el projector');
        $response->assertJsonPath('data.imagen', 'incidencias/prova.jpg');
        $response->assertJsonPath('data.idProfesor', 'PIN01');
        $response->assertJsonPath('data.prioridad', 2);
        $response->assertJsonPath('data.observaciones', 'Nomes a primera hora');
        $response->assertJsonPath('data.fecha', '2026-03-23');
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

        if (!Schema::connection('sqlite')->hasTable('incidencias')) {
            Schema::connection('sqlite')->create('incidencias', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('tipo')->nullable();
                $table->string('espacio')->nullable();
                $table->string('material')->nullable();
                $table->text('descripcion')->nullable();
                $table->string('imagen')->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedInteger('prioridad')->nullable();
                $table->string('observaciones')->nullable();
                $table->date('fecha')->nullable();
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
