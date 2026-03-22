<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiMaterialBajaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_material_baja_controller_feature_testing.sqlite');
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

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_show_no_falla_quan_material_associat_no_existeix(): void
    {
        DB::table('materiales_baja')->insert([
            'id' => 1,
            'idMaterial' => 9999,
            'idProfesor' => 'PTEST01',
            'motivo' => 'Baixa prova',
            'estado' => 1,
            'tipo' => 0,
        ]);

        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/materialbaja/1');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 9999);
        $response->assertJsonPath('data.descripció', '');
        $response->assertJsonPath('data.registre', '');
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('materiales')) {
            Schema::connection('sqlite')->create('materiales', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion', 255)->nullable();
                $table->string('nserieprov', 50)->nullable();
                $table->string('marca', 50)->nullable();
                $table->string('modelo', 50)->nullable();
                $table->unsignedTinyInteger('procedencia')->nullable();
                $table->unsignedTinyInteger('estado')->default(1);
                $table->string('espacio', 10)->nullable();
                $table->unsignedInteger('articulo_lote_id')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('materiales_baja')) {
            Schema::connection('sqlite')->create('materiales_baja', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idMaterial');
                $table->string('idProfesor', 10)->nullable();
                $table->string('motivo', 255)->nullable();
                $table->unsignedTinyInteger('estado')->default(0);
                $table->unsignedTinyInteger('tipo')->default(0);
                $table->string('nuevoEstado', 50)->nullable();
                $table->timestamps();
            });
        }
    }
}
