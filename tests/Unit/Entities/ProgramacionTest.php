<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Programacion;
use Tests\TestCase;

class ProgramacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        config()->set('constants.modulosSinProgramacion', ['TUT']);
        config()->set('constants.modulosNoLectivos', ['TUT']);

        $this->createSchema();
    }

    public function test_scope_departamento_filtra_pel_departament_indicat(): void
    {
        DB::table('modulo_ciclos')->insert([
            ['id' => 10, 'idModulo' => 'M1', 'idCiclo' => 1, 'idDepartamento' => 20],
            ['id' => 11, 'idModulo' => 'M2', 'idCiclo' => 1, 'idDepartamento' => 30],
        ]);

        DB::table('programaciones')->insert([
            ['id' => 1, 'idModuloCiclo' => 10, 'fichero' => 'a.pdf'],
            ['id' => 2, 'idModuloCiclo' => 11, 'fichero' => 'b.pdf'],
        ]);

        $ids = Programacion::query()->departamento(20)->pluck('id')->all();

        $this->assertSame([1], $ids);
    }

    public function test_scope_mis_programaciones_retorna_sols_moduls_assignats_al_professor(): void
    {
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'idCiclo' => 1, 'nombre' => 'Grup 1'],
            ['codigo' => 'G2', 'idCiclo' => 1, 'nombre' => 'Grup 2'],
        ]);

        DB::table('modulo_ciclos')->insert([
            ['id' => 10, 'idModulo' => 'M1', 'idCiclo' => 1, 'idDepartamento' => 20],
            ['id' => 11, 'idModulo' => 'M2', 'idCiclo' => 1, 'idDepartamento' => 20],
        ]);

        DB::table('programaciones')->insert([
            ['id' => 1, 'idModuloCiclo' => 10, 'fichero' => 'm1.pdf'],
            ['id' => 2, 'idModuloCiclo' => 11, 'fichero' => 'm2.pdf'],
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P1', 'modulo' => 'M1', 'idGrupo' => 'G1'],
            ['idProfesor' => 'P1', 'modulo' => null, 'idGrupo' => 'G1'],
            ['idProfesor' => 'P1', 'modulo' => 'TUT', 'idGrupo' => 'G1'],
        ]);

        $ids = Programacion::query()->misProgramaciones('P1')->pluck('id')->all();

        $this->assertSame([1], $ids);
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('programaciones');
        $schema->dropIfExists('horarios');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('modulo_ciclos');

        $schema->create('programaciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloCiclo');
            $table->string('fichero')->nullable();
            $table->unsignedTinyInteger('estado')->default(0);
            $table->timestamps();
        });

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo');
            $table->unsignedInteger('idCiclo');
            $table->unsignedInteger('idDepartamento');
            $table->unsignedTinyInteger('curso')->default(1);
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->unsignedInteger('idCiclo');
            $table->string('turno')->nullable();
            $table->timestamps();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->string('modulo')->nullable();
            $table->string('idGrupo')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('aula')->nullable();
            $table->string('dia_semana')->nullable();
            $table->unsignedInteger('sesion_orden')->nullable();
            $table->string('plantilla')->nullable();
            $table->timestamps();
        });
    }
}

