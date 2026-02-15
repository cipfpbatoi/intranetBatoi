<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\AlumnoFct;
use Tests\TestCase;

class AlumnoFctScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    public function test_scope_es_erasmus_filtra_ids_correctament(): void
    {
        DB::table('fcts')->insert([
            ['id' => 1, 'erasmus' => 1, 'asociacion' => 1],
            ['id' => 2, 'erasmus' => 0, 'asociacion' => 2],
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 10, 'idFct' => 1, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 11, 'idFct' => 2, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
        ]);

        $ids = AlumnoFct::query()->esErasmus()->pluck('id')->all();

        $this->assertSame([10], $ids);
    }

    public function test_scope_es_exempt_i_esta_sao_funcionen_amb_llistes_planes(): void
    {
        DB::table('fcts')->insert([
            ['id' => 1, 'erasmus' => 0, 'asociacion' => 2], // exempt
            ['id' => 2, 'erasmus' => 0, 'asociacion' => 1], // no exempt
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 20, 'idFct' => 1, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 21, 'idFct' => 2, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
        ]);

        $exempts = AlumnoFct::query()->esExempt()->pluck('id')->all();
        $sao = AlumnoFct::query()->estaSao()->pluck('id')->all();

        $this->assertSame([20], $exempts);
        $this->assertSame([21], $sao);
    }

    public function test_scope_activa_compara_columnes_horas_i_realizadas(): void
    {
        DB::table('alumno_fcts')->insert([
            ['id' => 30, 'idFct' => 1, 'calificacion' => null, 'correoAlumno' => 0, 'horas' => 120, 'realizadas' => 60],
            ['id' => 31, 'idFct' => 1, 'calificacion' => null, 'correoAlumno' => 0, 'horas' => 60, 'realizadas' => 60],
            ['id' => 32, 'idFct' => 1, 'calificacion' => 1, 'correoAlumno' => 0, 'horas' => 120, 'realizadas' => 20],
            ['id' => 33, 'idFct' => 1, 'calificacion' => null, 'correoAlumno' => 1, 'horas' => 120, 'realizadas' => 20],
        ]);

        $ids = AlumnoFct::query()->activa()->pluck('id')->all();

        $this->assertSame([30], $ids);
    }

    public function test_scope_mis_fcts_inclou_professor_i_substituit(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P0', 'sustituye_a' => null],
            ['dni' => 'P1', 'sustituye_a' => 'P0'],
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 40, 'idFct' => 1, 'idProfesor' => 'P1', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 41, 'idFct' => 1, 'idProfesor' => 'P0', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 42, 'idFct' => 1, 'idProfesor' => 'PX', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
        ]);

        $ids = AlumnoFct::query()->misFcts('P1')->pluck('id')->all();

        $this->assertSame([40, 41], $ids);
    }

    public function test_scope_mis_proyectos_filtra_per_aval_i_projecte_null(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P0', 'sustituye_a' => null],
            ['dni' => 'P1', 'sustituye_a' => 'P0'],
        ]);

        DB::table('fcts')->insert([
            ['id' => 10, 'erasmus' => 0, 'asociacion' => 1], // aval
            ['id' => 11, 'erasmus' => 0, 'asociacion' => 3], // dual
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 50, 'idFct' => 10, 'idProfesor' => 'P1', 'calProyecto' => null, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 51, 'idFct' => 10, 'idProfesor' => 'P0', 'calProyecto' => 7, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 52, 'idFct' => 11, 'idProfesor' => 'P1', 'calProyecto' => null, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
        ]);

        $ids = AlumnoFct::query()->misProyectos('P1')->pluck('id')->all();

        $this->assertSame([50], $ids);
    }

    public function test_scopes_mis_dual_i_mis_convalidados_filtren_associacio_i_substitucions(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P0', 'sustituye_a' => null],
            ['dni' => 'P1', 'sustituye_a' => 'P0'],
        ]);

        DB::table('fcts')->insert([
            ['id' => 20, 'erasmus' => 0, 'asociacion' => 3], // dual
            ['id' => 21, 'erasmus' => 0, 'asociacion' => 2], // exempt
            ['id' => 22, 'erasmus' => 0, 'asociacion' => 1], // normal
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 60, 'idFct' => 20, 'idProfesor' => 'P1', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 61, 'idFct' => 20, 'idProfesor' => 'P0', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 62, 'idFct' => 20, 'idProfesor' => 'PX', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 63, 'idFct' => 21, 'idProfesor' => 'P1', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
            ['id' => 64, 'idFct' => 22, 'idProfesor' => 'P1', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0],
        ]);

        $dualIds = AlumnoFct::query()->misDual('P1')->pluck('id')->all();
        $convalidatsIds = AlumnoFct::query()->misConvalidados('P1')->pluck('id')->all();

        $this->assertSame([60, 61], $dualIds);
        $this->assertSame([63], $convalidatsIds);
    }

    public function test_scopes_es_fct_es_aval_i_es_dual_filtren_per_associacio(): void
    {
        DB::table('fcts')->insert([
            ['id' => 30, 'erasmus' => 0, 'asociacion' => 1], // fct + aval
            ['id' => 31, 'erasmus' => 0, 'asociacion' => 2], // aval
            ['id' => 32, 'erasmus' => 0, 'asociacion' => 3], // dual
            ['id' => 33, 'erasmus' => 0, 'asociacion' => 4], // fct
            ['id' => 34, 'erasmus' => 0, 'asociacion' => 5], // fct
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 70, 'idFct' => 30, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
            ['id' => 71, 'idFct' => 31, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
            ['id' => 72, 'idFct' => 32, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
            ['id' => 73, 'idFct' => 33, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
            ['id' => 74, 'idFct' => 34, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
        ]);

        $idsFct = AlumnoFct::query()->esFct()->pluck('id')->all();
        $idsAval = AlumnoFct::query()->esAval()->pluck('id')->all();
        $idsDual = AlumnoFct::query()->esDual()->pluck('id')->all();

        $this->assertSame([70, 73, 74], $idsFct);
        $this->assertSame([70, 71], $idsAval);
        $this->assertSame([72], $idsDual);
    }

    public function test_scopes_ha_empezado_i_no_ha_acabado_filtren_per_data(): void
    {
        DB::table('alumno_fcts')->insert([
            ['id' => 80, 'idFct' => 1, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2000-01-01', 'hasta' => '2099-12-31'],
            ['id' => 81, 'idFct' => 1, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2099-01-01', 'hasta' => '2099-12-31'],
            ['id' => 82, 'idFct' => 1, 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2000-01-01', 'hasta' => '2000-12-31'],
        ]);

        $haEmpezado = AlumnoFct::query()->haEmpezado()->pluck('id')->all();
        $noHaAcabado = AlumnoFct::query()->noHaAcabado()->pluck('id')->all();

        $this->assertContains(80, $haEmpezado);
        $this->assertContains(82, $haEmpezado);
        $this->assertNotContains(81, $haEmpezado);

        $this->assertContains(80, $noHaAcabado);
        $this->assertContains(81, $noHaAcabado);
        $this->assertNotContains(82, $noHaAcabado);
    }

    public function test_scope_totes_fcts_reutilitza_mis_fcts(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P0', 'sustituye_a' => null],
            ['dni' => 'P1', 'sustituye_a' => 'P0'],
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 90, 'idFct' => 1, 'idProfesor' => 'P1', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
            ['id' => 91, 'idFct' => 1, 'idProfesor' => 'P0', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
            ['id' => 92, 'idFct' => 1, 'idProfesor' => 'PX', 'horas' => 100, 'realizadas' => 0, 'correoAlumno' => 0, 'desde' => '2025-01-01', 'hasta' => '2025-01-31'],
        ]);

        $idsMis = AlumnoFct::query()->misFcts('P1')->pluck('id')->all();
        $idsTotes = AlumnoFct::query()->totesFcts('P1')->pluck('id')->all();

        $this->assertSame($idsMis, $idsTotes);
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('alumno_fcts');
        $schema->dropIfExists('fcts');
        $schema->dropIfExists('profesores');

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('erasmus')->default(0);
            $table->unsignedTinyInteger('asociacion')->default(1);
        });

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('sustituye_a', 10)->nullable();
        });

        $schema->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idFct');
            $table->string('idProfesor', 10)->nullable();
            $table->unsignedInteger('calificacion')->nullable();
            $table->unsignedTinyInteger('correoAlumno')->default(0);
            $table->unsignedInteger('horas')->default(0);
            $table->unsignedInteger('realizadas')->default(0);
            $table->unsignedInteger('calProyecto')->nullable();
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
        });
    }
}
