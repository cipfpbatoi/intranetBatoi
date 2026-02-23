<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Services\School\ModuloGrupoService;
use Tests\TestCase;

class ModuloGrupoTest extends TestCase
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
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('horarios');

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo', 20);
            $table->unsignedInteger('idCiclo');
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('turno', 1)->nullable();
            $table->timestamps();
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

    public function test_mis_modulos_ignora_horaris_amb_grup_orfe_i_no_falla(): void
    {
        DB::table('modulo_ciclos')->insert([
            'id' => 1,
            'idModulo' => 'MTEST',
            'idCiclo' => 1,
        ]);

        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'idCiclo' => 1,
            'nombre' => 'Grup 1',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('modulo_grupos')->insert([
            'id' => 10,
            'idModuloCiclo' => 1,
            'idGrupo' => 'G1',
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P1', 'modulo' => 'MTEST', 'idGrupo' => 'G404'],
            ['idProfesor' => 'P1', 'modulo' => 'MTEST', 'idGrupo' => 'G1'],
        ]);

        $result = app(ModuloGrupoService::class)->misModulos('P1');

        $this->assertCount(1, $result);
        $this->assertSame(10, $result[0]->id);
    }

    public function test_mis_modulos_no_duplica_resultats_amb_horaris_repetits(): void
    {
        DB::table('modulo_ciclos')->insert([
            'id' => 2,
            'idModulo' => 'MREP',
            'idCiclo' => 1,
        ]);

        DB::table('grupos')->insert([
            'codigo' => 'GR',
            'idCiclo' => 1,
            'nombre' => 'Grup Repetit',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('modulo_grupos')->insert([
            'id' => 20,
            'idModuloCiclo' => 2,
            'idGrupo' => 'GR',
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P2', 'modulo' => 'MREP', 'idGrupo' => 'GR'],
            ['idProfesor' => 'P2', 'modulo' => 'MREP', 'idGrupo' => 'GR'],
        ]);

        $result = app(ModuloGrupoService::class)->misModulos('P2');

        $this->assertCount(1, $result);
        $this->assertSame(20, $result[0]->id);
    }
}
