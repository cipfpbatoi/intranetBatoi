<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiResourceControllerErrorContractFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_resource_error_contract_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_show_not_found_returns_404(): void
    {
        $this->insertProfesor('PRC01');
        $user = Profesor::on('sqlite')->findOrFail('PRC01');
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/profesor/NO_EXISTIX');

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
    }

    public function test_store_internal_error_returns_500_and_generic_message(): void
    {
        $this->insertProfesor('PRC02');
        $user = Profesor::on('sqlite')->findOrFail('PRC02');
        $this->actingAs($user, 'api');

        // Forcem error intern: "dni" és PK obligatòria i no és fillable,
        // així que el create() del controlador base acabarà en excepció SQL.
        $response = $this->postJson('/api/profesor', [
            'email' => 'error@test.local',
        ]);

        $response->assertStatus(500);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Internal server error');
    }

    public function test_update_internal_error_returns_500_and_generic_message(): void
    {
        $this->insertProfesor('PRC03');
        $user = Profesor::on('sqlite')->findOrFail('PRC03');
        $this->actingAs($user, 'api');

        // Forcem error intern sobre un model amb taula inexistent en este test (cursos).
        $response = $this->putJson('/api/curso/1', [
            'nombre' => 'Canvi',
        ]);

        $response->assertStatus(500);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Internal server error');
    }

    private function createSchema(): void
    {
        if (Schema::connection('sqlite')->hasTable('profesores')) {
            return;
        }

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
