<?php

declare(strict_types=1);

namespace Tests\Unit\Services\School;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Modulo_grupo;
use Intranet\Services\School\ModuloGrupoService;
use Tests\TestCase;

class ModuloGrupoServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('modulo_grupos');
        $schema->dropIfExists('modulo_ciclos');
        $schema->dropIfExists('horarios');

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo', 20);
            $table->unsignedInteger('idCiclo')->nullable();
        });

        $schema->create('modulo_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloCiclo');
            $table->string('idGrupo', 10);
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10)->nullable();
            $table->string('modulo', 20)->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->string('ocupacion', 10)->nullable();
            $table->string('aula', 10)->nullable();
            $table->string('dia_semana', 10)->nullable();
            $table->unsignedInteger('sesion_orden')->nullable();
            $table->unsignedTinyInteger('plantilla')->nullable();
            $table->timestamps();
        });
    }

    public function test_profesor_ids_son_uniques_i_filtren_nuls(): void
    {
        DB::table('modulo_ciclos')->insert([
            'id' => 1,
            'idModulo' => 'M10',
            'idCiclo' => 1,
        ]);

        DB::table('modulo_grupos')->insert([
            'id' => 10,
            'idModuloCiclo' => 1,
            'idGrupo' => 'G1',
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P1', 'modulo' => 'M10', 'idGrupo' => 'G1'],
            ['idProfesor' => 'P1', 'modulo' => 'M10', 'idGrupo' => 'G1'],
            ['idProfesor' => 'P2', 'modulo' => 'M10', 'idGrupo' => 'G1'],
            ['idProfesor' => null, 'modulo' => 'M10', 'idGrupo' => 'G1'],
        ]);

        $registro = Modulo_grupo::findOrFail(10);
        $service = app(ModuloGrupoService::class);

        $this->assertSame(['P1', 'P2'], $service->profesorIds($registro)->values()->all());
    }
}
