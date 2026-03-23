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
 * Proves feature del payload `edit()` de tasques via JsonResource.
 */
class ApiTaskEditResourceFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_task_edit_resource_testing.sqlite');
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
        $this->authenticateProfesor('PTK01');
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('tasks');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_task_edit_usa_resource_explicita(): void
    {
        DB::table('tasks')->insert([
            'id' => 41,
            'descripcion' => 'Tasqueta de prova',
            'vencimiento' => '2026-05-08',
            'destinatario' => 3,
            'informativa' => 1,
            'fichero' => 'tasks/test.pdf',
            'enlace' => 'https://example.com',
            'action' => 2,
            'activa' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/task/41/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.descripcion', 'Tasqueta de prova');
        $response->assertJsonPath('data.vencimiento', '08-05-2026');
        $response->assertJsonPath('data.destinatario', 3);
        $response->assertJsonPath('data.informativa', 1);
        $response->assertJsonPath('data.action', 2);
        $response->assertJsonPath('data.activa', 1);
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

        if (!Schema::connection('sqlite')->hasTable('tasks')) {
            Schema::connection('sqlite')->create('tasks', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion')->nullable();
                $table->date('vencimiento')->nullable();
                $table->unsignedInteger('destinatario')->nullable();
                $table->unsignedTinyInteger('informativa')->default(0);
                $table->string('fichero')->nullable();
                $table->string('enlace')->nullable();
                $table->unsignedInteger('action')->nullable();
                $table->unsignedTinyInteger('activa')->default(1);
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
