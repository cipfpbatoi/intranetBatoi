<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Modulo_grupo;
use Tests\TestCase;

class ModuloGrupoAccessorsTest extends TestCase
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
        $schema->dropIfExists('profesores');

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo', 20)->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('idDepartamento')->nullable();
            $table->unsignedTinyInteger('curso')->default(1);
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
            $table->unsignedInteger('idModuloCiclo')->nullable();
            $table->string('idGrupo', 10)->nullable();
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

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->unsignedInteger('rol')->default(0);
            $table->unsignedTinyInteger('activo')->default(1);
            $table->string('departamento')->nullable();
            $table->string('codigo')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function test_accessors_no_fallen_quan_modulo_ciclo_no_existeix(): void
    {
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'idCiclo' => 1,
            'nombre' => 'Grup 1',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('modulo_grupos')->insert([
            'id' => 1,
            'idModuloCiclo' => 9999,
            'idGrupo' => 'G1',
        ]);

        $registro = Modulo_grupo::findOrFail(1);

        $this->assertSame('', $registro->Xmodulo);
        $this->assertSame('', $registro->Xciclo);
        $this->assertSame('', $registro->Xdepartamento);
        $this->assertSame('presential', $registro->Xtorn);
        $this->assertSame('', $registro->ProgramacioLink);
        $this->assertSame([], $registro->Profesores());
    }

    public function test_profesor_accessor_resol_noms_en_una_consulta_i_ordre_horari(): void
    {
        DB::table('modulo_ciclos')->insert([
            'id' => 2,
            'idModulo' => 'M20',
            'idCiclo' => 1,
            'idDepartamento' => 1,
            'curso' => 1,
        ]);

        DB::table('grupos')->insert([
            'codigo' => 'G2',
            'idCiclo' => 1,
            'nombre' => 'Grup 2',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('modulo_grupos')->insert([
            'id' => 2,
            'idModuloCiclo' => 2,
            'idGrupo' => 'G2',
        ]);

        DB::table('profesores')->insert([
            [
                'dni' => 'P10',
                'nombre' => 'ALFA',
                'apellido1' => 'U',
                'apellido2' => 'UNO',
                'rol' => 2,
                'activo' => 1,
            ],
            [
                'dni' => 'P20',
                'nombre' => 'BETA',
                'apellido1' => 'D',
                'apellido2' => 'DOS',
                'rol' => 2,
                'activo' => 1,
            ],
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P20', 'modulo' => 'M20', 'idGrupo' => 'G2'],
            ['idProfesor' => 'P10', 'modulo' => 'M20', 'idGrupo' => 'G2'],
            ['idProfesor' => 'P10', 'modulo' => 'M20', 'idGrupo' => 'G2'],
        ]);

        $registro = Modulo_grupo::findOrFail(2);

        $this->assertSame('Beta D Dos Alfa U Uno ', $registro->profesor);
    }
}
