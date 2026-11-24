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
 * Tests d'autorització de l'API d'espais.
 */
class ApiEspacioControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_espacio_controller_feature_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('rol')->default(3);
            $table->boolean('activo')->default(true);
        });
        Schema::connection('sqlite')->create('espacios', function (Blueprint $table): void {
            $table->string('aula', 10)->primary();
            $table->string('descripcion', 100);
            $table->unsignedTinyInteger('idDepartamento');
            $table->string('gMati', 5)->nullable();
            $table->string('gVesprada', 5)->nullable();
            $table->boolean('reservable')->default(false);
        });
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('espacios');
        Schema::connection('sqlite')->dropIfExists('profesores');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_administracio_no_pot_crear_espais_per_l_api(): void
    {
        $this->assertStoreIsForbiddenForRole(config('roles.rol.administrador'));
    }

    public function test_manteniment_no_pot_crear_espais_per_l_api(): void
    {
        $this->assertStoreIsForbiddenForRole(config('roles.rol.mantenimiento'));
    }

    public function test_direccio_pot_crear_espais_per_l_api(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'APIDIR01',
            'rol' => config('roles.rol.direccion'),
            'activo' => 1,
        ]);
        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail('APIDIR01'));

        $response = $this->postJson('/api/espacio', [
            'aula' => 'API-DIR',
            'descripcion' => 'Espai de Direcció',
            'idDepartamento' => 99,
        ]);

        $response->assertOk();
        $this->assertSame(1, DB::table('espacios')->where('aula', 'API-DIR')->count());
    }

    private function assertStoreIsForbiddenForRole(int $role): void
    {
        DB::table('profesores')->insert([
            'dni' => 'API' . $role,
            'rol' => $role,
            'activo' => 1,
        ]);
        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail('API' . $role));

        $response = $this->postJson('/api/espacio', [
            'aula' => 'API' . $role,
            'descripcion' => 'Espai no autoritzat',
            'idDepartamento' => 99,
        ]);

        $response->assertForbidden();
        $this->assertSame(0, DB::table('espacios')->where('aula', 'API' . $role)->count());
    }
}
