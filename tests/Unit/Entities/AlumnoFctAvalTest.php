<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\AlumnoFct;
use Tests\TestCase;

class AlumnoFctAvalTest extends TestCase
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

    public function test_scopes_actas_filtren_correctament(): void
    {
        DB::table('alumno_fcts')->insert([
            ['id' => 1, 'idAlumno' => 'A1', 'idFct' => 10, 'actas' => 1, 'calificacion' => null, 'correoAlumno' => 0, 'horas' => 40],
            ['id' => 2, 'idAlumno' => 'A1', 'idFct' => 10, 'actas' => 2, 'calificacion' => 1, 'correoAlumno' => 0, 'horas' => 40],
            ['id' => 3, 'idAlumno' => 'A1', 'idFct' => 10, 'actas' => 3, 'calificacion' => 1, 'correoAlumno' => 0, 'horas' => 40],
        ]);

        $this->assertSame([1], AlumnoFct::query()->noAval()->pluck('id')->all());
        $this->assertSame([2], AlumnoFct::query()->aval()->pluck('id')->all());
        $this->assertSame([3], AlumnoFct::query()->pendiente()->pluck('id')->all());
    }

    public function test_scope_pendiente_notificar_filtra_per_calificacio_i_correu(): void
    {
        DB::table('alumno_fcts')->insert([
            ['id' => 11, 'idAlumno' => 'A2', 'idFct' => 10, 'actas' => 2, 'calificacion' => 1, 'correoAlumno' => 0, 'horas' => 20],
            ['id' => 12, 'idAlumno' => 'A2', 'idFct' => 10, 'actas' => 2, 'calificacion' => 1, 'correoAlumno' => 1, 'horas' => 20],
            ['id' => 13, 'idAlumno' => 'A2', 'idFct' => 10, 'actas' => 2, 'calificacion' => 0, 'correoAlumno' => 0, 'horas' => 20],
        ]);

        $this->assertSame([11], AlumnoFct::query()->pendienteNotificar()->pluck('id')->all());
    }

    public function test_horas_total_suma_totes_les_linies_no_notificades_del_alumne(): void
    {
        DB::table('alumno_fcts')->insert([
            ['id' => 21, 'idAlumno' => 'A3', 'idFct' => 10, 'actas' => 2, 'calificacion' => 1, 'correoAlumno' => 0, 'horas' => 120],
            ['id' => 22, 'idAlumno' => 'A3', 'idFct' => 11, 'actas' => 2, 'calificacion' => 1, 'correoAlumno' => 0, 'horas' => 80],
            ['id' => 23, 'idAlumno' => 'A3', 'idFct' => 12, 'actas' => 2, 'calificacion' => 1, 'correoAlumno' => 1, 'horas' => 50],
        ]);

        $nonNotified = AlumnoFct::findOrFail(21);
        $notified = AlumnoFct::findOrFail(23);

        $this->assertSame(200, (int) $nonNotified->horasTotal);
        $this->assertSame(50, (int) $notified->horasTotal);
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('alumno_fcts');
        $schema->dropIfExists('fcts');

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('asociacion')->default(1);
        });

        $schema->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno')->nullable();
            $table->unsignedInteger('idFct');
            $table->string('idProfesor')->nullable();
            $table->unsignedTinyInteger('actas')->default(0);
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->unsignedTinyInteger('correoAlumno')->default(0);
            $table->unsignedInteger('horas')->default(0);
            $table->unsignedInteger('realizadas')->default(0);
            $table->unsignedTinyInteger('calProyecto')->nullable();
        });
    }
}
