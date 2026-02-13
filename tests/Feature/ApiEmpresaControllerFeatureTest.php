<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiEmpresaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_empresa_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('empresas');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_convenio_filtra_i_ordena_per_nom(): void
    {
        DB::table('empresas')->insert([
            [
                'id' => 1,
                'europa' => 0,
                'concierto' => 10,
                'nombre' => 'Zulu',
                'direccion' => 'C/ Z',
                'localidad' => 'Alcoi',
                'telefono' => '111111111',
                'email' => 'zulu@test.local',
                'cif' => 'Z0000000',
                'actividad' => 'Act Z',
                'fichero' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'europa' => 0,
                'concierto' => 5,
                'nombre' => 'Alpha',
                'direccion' => 'C/ A',
                'localidad' => 'Alcoi',
                'telefono' => '222222222',
                'email' => 'alpha@test.local',
                'cif' => 'A0000000',
                'actividad' => 'Act A',
                'fichero' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'europa' => 1, // Exclosa per europa
                'concierto' => 8,
                'nombre' => 'Europa',
                'direccion' => 'C/ E',
                'localidad' => 'Alcoi',
                'telefono' => '333333333',
                'email' => 'europa@test.local',
                'cif' => 'E0000000',
                'actividad' => 'Act E',
                'fichero' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'europa' => 0,
                'concierto' => 0, // Exclosa per concierto
                'nombre' => 'SenseConveni',
                'direccion' => 'C/ S',
                'localidad' => 'Alcoi',
                'telefono' => '444444444',
                'email' => 'sense@test.local',
                'cif' => 'S0000000',
                'actividad' => 'Act S',
                'fichero' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/convenio');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.nombre', 'Alpha');
        $response->assertJsonPath('data.1.nombre', 'Zulu');
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'concierto',
                    'nombre',
                    'direccion',
                    'localidad',
                    'telefono',
                    'email',
                    'cif',
                    'actividad',
                    'conveni',
                    'fichero',
                ],
            ],
        ]);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('empresas')) {
            Schema::connection('sqlite')->create('empresas', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedTinyInteger('europa')->default(0);
                $table->unsignedInteger('concierto')->default(0);
                $table->string('cif')->nullable();
                $table->string('nombre')->nullable();
                $table->string('email')->nullable();
                $table->string('direccion')->nullable();
                $table->string('localidad')->nullable();
                $table->string('telefono')->nullable();
                $table->string('actividad')->nullable();
                $table->string('fichero')->nullable();
                $table->timestamps();
            });
        }
    }
}

