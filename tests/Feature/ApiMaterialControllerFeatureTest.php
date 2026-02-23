<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiMaterialControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_material_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('materiales_baja');
        Schema::connection('sqlite')->dropIfExists('materiales');
        Schema::connection('sqlite')->dropIfExists('articulos_lote');
        Schema::connection('sqlite')->dropIfExists('articulos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_inventario_retorna_401_si_no_hi_ha_token_ni_usuari_api(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/inventario');

        $response->assertStatus(401);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Unauthorized');
    }

    public function test_inventario_accepta_token_legacy_i_retorna_dades(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'PMAT001',
            'rol' => config('roles.rol.direccion'),
            'departamento' => 1,
            'api_token' => 'token-material-test',
            'activo' => 1,
        ]);

        DB::table('articulos')->insert([
            'id' => 1,
            'descripcion' => 'Router',
        ]);

        DB::table('articulos_lote')->insert([
            'id' => 10,
            'lote_id' => 'L-001',
            'articulo_id' => 1,
            'unidades' => 1,
        ]);

        DB::table('materiales')->insert([
            'id' => 100,
            'descripcion' => 'Router principal',
            'espacio' => 'AULA1',
            'estado' => 1,
            'inventariable' => 1,
            'articulo_lote_id' => 10,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/inventario?api_token=token-material-test');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $data = $response->json('data');
        $items = is_array($data) && array_key_exists('data', $data) ? $data['data'] : $data;

        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertSame(100, $items[0]['id']);
        $this->assertSame('Router', $items[0]['articulo']);
    }

    public function test_inventario_no_falla_si_articulo_lote_es_orfe(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'PMAT002',
            'rol' => config('roles.rol.direccion'),
            'departamento' => 1,
            'api_token' => 'token-material-orfe',
            'activo' => 1,
        ]);

        DB::table('materiales')->insert([
            'id' => 101,
            'descripcion' => 'Switch de prova',
            'espacio' => 'AULA2',
            'estado' => 1,
            'inventariable' => 1,
            'articulo_lote_id' => 9999,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/inventario?api_token=token-material-orfe');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $data = $response->json('data');
        $items = is_array($data) && array_key_exists('data', $data) ? $data['data'] : $data;

        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertSame(101, $items[0]['id']);
        $this->assertSame('', $items[0]['articulo']);
    }

    public function test_espai_retorna_401_si_no_hi_ha_token_ni_usuari_api(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/inventario/AULA1');

        $response->assertStatus(401);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Unauthorized');
    }

    public function test_espai_aplica_filtre_per_ubicacio_amb_token_legacy(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'PMAT003',
            'rol' => config('roles.rol.direccion'),
            'departamento' => 1,
            'api_token' => 'token-material-espai',
            'activo' => 1,
        ]);

        DB::table('articulos')->insert([
            ['id' => 11, 'descripcion' => 'Pantalla'],
            ['id' => 12, 'descripcion' => 'Teclat'],
        ]);

        DB::table('articulos_lote')->insert([
            ['id' => 110, 'lote_id' => 'L-011', 'articulo_id' => 11, 'unidades' => 1],
            ['id' => 120, 'lote_id' => 'L-012', 'articulo_id' => 12, 'unidades' => 1],
        ]);

        DB::table('materiales')->insert([
            [
                'id' => 111,
                'descripcion' => 'Pantalla aula 1',
                'espacio' => 'AULA1',
                'estado' => 1,
                'inventariable' => 1,
                'articulo_lote_id' => 110,
            ],
            [
                'id' => 121,
                'descripcion' => 'Teclat aula 2',
                'espacio' => 'AULA2',
                'estado' => 1,
                'inventariable' => 1,
                'articulo_lote_id' => 120,
            ],
        ]);

        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/inventario/AULA1?api_token=token-material-espai');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $data = $response->json('data');
        $items = is_array($data) && array_key_exists('data', $data) ? $data['data'] : $data;

        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertSame(111, $items[0]['id']);
        $this->assertSame('AULA1', $items[0]['espacio']);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('rol')->default(3);
                $table->unsignedInteger('departamento')->nullable();
                $table->string('api_token', 80)->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('articulos')) {
            Schema::connection('sqlite')->create('articulos', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion', 200)->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('articulos_lote')) {
            Schema::connection('sqlite')->create('articulos_lote', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('lote_id', 12)->nullable();
                $table->unsignedInteger('articulo_id');
                $table->unsignedSmallInteger('unidades')->default(1);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('materiales')) {
            Schema::connection('sqlite')->create('materiales', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion', 255);
                $table->string('espacio', 10);
                $table->unsignedTinyInteger('estado')->default(1);
                $table->unsignedTinyInteger('inventariable')->default(1);
                $table->unsignedInteger('articulo_lote_id')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('materiales_baja')) {
            Schema::connection('sqlite')->create('materiales_baja', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idMaterial');
                $table->unsignedTinyInteger('tipo')->default(0);
            });
        }
    }
}
