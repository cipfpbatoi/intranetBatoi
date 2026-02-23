<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MaterialControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('material_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('materiales');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_copy_retorna_404_quan_material_no_existeix(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->get('/material/999999/copy');

        $response->assertStatus(404);
    }

    public function test_incidencia_retorna_404_quan_material_no_existeix(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->get('/material/999999/incidencia');

        $response->assertStatus(404);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('materiales')) {
            Schema::connection('sqlite')->create('materiales', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion', 255)->nullable();
                $table->string('espacio', 10)->nullable();
                $table->smallInteger('unidades')->default(1);
            });
        }
    }
}
