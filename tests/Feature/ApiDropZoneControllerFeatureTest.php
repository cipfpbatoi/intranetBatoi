<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiDropZoneControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_dropzone_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('adjuntos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_get_attached_retorna_fitxers_del_path(): void
    {
        $this->insertProfesor('PDZ01', 'token-dz-01');
        $user = Profesor::on('sqlite')->findOrFail('PDZ01');
        $this->actingAs($user, 'api');

        DB::table('adjuntos')->insert([
            'name' => 'A56 signat',
            'owner' => 'PDZ01',
            'title' => 'a56_2434',
            'size' => 12345,
            'extension' => 'pdf',
            'route' => 'alumnofctaval/2434',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/getAttached/alumnofctaval/2434?api_token=token-dz-01');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'A56 signat');
        $response->assertJsonPath('data.0.file', 'a56_2434.pdf');
    }

    public function test_remove_attached_denega_si_no_es_propietari(): void
    {
        $this->insertProfesor('PDZ02', 'token-dz-02');
        $this->insertProfesor('PDZ03', 'token-dz-03');
        $user = Profesor::on('sqlite')->findOrFail('PDZ02');
        $this->actingAs($user, 'api');

        DB::table('adjuntos')->insert([
            'name' => 'A56 extern',
            'owner' => 'PDZ03',
            'title' => 'a56_extern',
            'size' => 500,
            'extension' => 'pdf',
            'route' => 'alumnofctaval/2434',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/removeAttached/alumnofctaval/2434/A56%20extern?api_token=token-dz-02');

        $response->assertStatus(400);
        $response->assertSeeText("Sense permisos, no ets el propietari");
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

        if (!Schema::connection('sqlite')->hasTable('adjuntos')) {
            Schema::connection('sqlite')->create('adjuntos', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('owner', 10)->nullable();
                $table->text('referencesTo')->nullable();
                $table->string('title');
                $table->unsignedBigInteger('size')->default(0);
                $table->string('extension', 10);
                $table->string('route');
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, string $apiToken): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => $apiToken,
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
