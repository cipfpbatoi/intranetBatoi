<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Grupo;
use Tests\TestCase;

class GrupoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('alumnos_grupos');
        $schema->dropIfExists('alumnos');
        $schema->dropIfExists('horarios');
        $schema->dropIfExists('ciclos');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('sustituye_a', 10)->nullable();
            $table->timestamps();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('curso')->nullable();
            $table->timestamps();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('departamento')->nullable();
            $table->string('ciclo')->nullable();
            $table->string('normativa')->nullable();
            $table->unsignedTinyInteger('tipo')->nullable();
            $table->timestamps();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->timestamps();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('idGrupo')->nullable();
            $table->string('dia_semana')->nullable();
            $table->unsignedInteger('sesion_orden')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('modulo')->nullable();
            $table->timestamps();
        });
    }

    public function test_scope_q_tutor_inclou_grups_del_professor_i_del_sustituit(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P0', 'sustituye_a' => null, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P1', 'sustituye_a' => 'P0', 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P2', 'sustituye_a' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup 1', 'tutor' => 'P1', 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G2', 'nombre' => 'Grup 2', 'tutor' => 'P0', 'idCiclo' => 1, 'curso' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G3', 'nombre' => 'Grup 3', 'tutor' => 'P2', 'idCiclo' => 2, 'curso' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $codigos = Grupo::query()->QTutor('P1')->pluck('codigo')->sort()->values()->all();

        $this->assertSame(['G1', 'G2'], $codigos);
    }

    public function test_scope_largest_by_alumnes_ordena_per_comptador_i_per_codi(): void
    {
        DB::table('grupos')->insert([
            ['codigo' => 'G0', 'nombre' => 'Grup 0', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G1', 'nombre' => 'Grup 1', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G2', 'nombre' => 'Grup 2', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('alumnos')->insert([
            ['nia' => 'A1', 'created_at' => now(), 'updated_at' => now()],
            ['nia' => 'A2', 'created_at' => now(), 'updated_at' => now()],
            ['nia' => 'A3', 'created_at' => now(), 'updated_at' => now()],
            ['nia' => 'A4', 'created_at' => now(), 'updated_at' => now()],
            ['nia' => 'A5', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A1', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A2', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A3', 'idGrupo' => 'G2'],
            ['idAlumno' => 'A4', 'idGrupo' => 'G2'],
            ['idAlumno' => 'A5', 'idGrupo' => 'G0'],
        ]);

        $codigos = Grupo::query()->largestByAlumnes()->pluck('codigo')->values()->all();

        $this->assertSame(['G1', 'G2', 'G0'], $codigos);
    }

    public function test_scope_curso_filtra_correctament(): void
    {
        DB::table('grupos')->insert([
            ['codigo' => 'GA', 'nombre' => 'A', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'GB', 'nombre' => 'B', 'tutor' => null, 'idCiclo' => 2, 'curso' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $codigos = Grupo::query()->Curso(2)->pluck('codigo')->all();

        $this->assertSame(['GB'], $codigos);
    }

    public function test_scope_departamento_filtra_grups_per_cicle(): void
    {
        DB::table('ciclos')->insert([
            ['id' => 1, 'departamento' => 10, 'ciclo' => 'C1', 'normativa' => 'LOE', 'tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'departamento' => 20, 'ciclo' => 'C2', 'normativa' => 'LOE', 'tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'G10', 'nombre' => 'Dep10', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G20', 'nombre' => 'Dep20', 'tutor' => null, 'idCiclo' => 2, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $codigos = Grupo::query()->Departamento(10)->pluck('codigo')->all();

        $this->assertSame(['G10'], $codigos);
    }

    public function test_scope_mis_grupos_descarta_moduls_no_lectius(): void
    {
        config(['constants.modulosNoLectivos' => ['TU01CF', 'TU02CF']]);

        DB::table('profesores')->insert([
            ['dni' => 'PX', 'sustituye_a' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'GL', 'nombre' => 'Lectiu', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'GN', 'nombre' => 'No lectiu', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('horarios')->insert([
            [
                'idProfesor' => 'PX',
                'idGrupo' => 'GL',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PX',
                'idGrupo' => 'GN',
                'dia_semana' => 'L',
                'sesion_orden' => 2,
                'ocupacion' => null,
                'modulo' => 'TU01CF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $codigos = Grupo::query()->MisGrupos((object) ['dni' => 'PX'])->pluck('codigo')->all();

        $this->assertSame(['GL'], $codigos);
    }

    public function test_accessor_proyecto_retorna_false_si_no_hi_ha_cicle(): void
    {
        $grupo = new Grupo();
        $grupo->curso = 2;
        $grupo->idCiclo = null;

        $this->assertFalse($grupo->proyecto);
    }

    public function test_accessor_xtutor_retorna_buit_si_no_hi_ha_tutor(): void
    {
        $grupo = new Grupo();
        $grupo->tutor = null;

        $this->assertSame('', $grupo->xtutor);
    }

    public function test_scope_matriculado_filtra_per_id_alumne(): void
    {
        DB::table('grupos')->insert([
            ['codigo' => 'GM1', 'nombre' => 'Matriculat 1', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'GM2', 'nombre' => 'Matriculat 2', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'AX1', 'idGrupo' => 'GM1'],
            ['idAlumno' => 'AX2', 'idGrupo' => 'GM2'],
        ]);

        $codigos = Grupo::query()->Matriculado('AX1')->pluck('codigo')->all();

        $this->assertSame(['GM1'], $codigos);
    }

    public function test_scope_mi_grupo_modulo_filtra_grups_per_professor_i_modul(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'PZ', 'sustituye_a' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'MG1', 'nombre' => 'M1', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'MG2', 'nombre' => 'M2', 'tutor' => null, 'idCiclo' => 1, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('horarios')->insert([
            [
                'idProfesor' => 'PZ',
                'idGrupo' => 'MG1',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PZ',
                'idGrupo' => 'MG2',
                'dia_semana' => 'L',
                'sesion_orden' => 2,
                'ocupacion' => null,
                'modulo' => 'M02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $codigos = Grupo::query()->MiGrupoModulo('PZ', 'M01')->pluck('codigo')->all();

        $this->assertSame(['MG1'], $codigos);
    }
}
