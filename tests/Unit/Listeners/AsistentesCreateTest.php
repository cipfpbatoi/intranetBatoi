<?php

namespace Tests\Unit\Listeners;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Grupo;
use Intranet\Entities\Reunion;
use Intranet\Listeners\AsistentesCreate;
use Tests\TestCase;

/**
 * Tests unitaris de l'assignació automàtica d'assistents a reunions.
 */
class AsistentesCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('tablas.tipoReunion.7.colectivo', 'Grupo');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('alumno_reuniones');
        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('alumnos');

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 12)->primary();
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo');
            $table->unsignedTinyInteger('numero');
            $table->string('idProfesor', 10);
            $table->timestamps();
        });

        $schema->create('alumno_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->string('idAlumno', 12);
            $table->unsignedTinyInteger('capacitats')->default(0);
        });
    }

    public function test_assigna_alumnes_extraordinaries_usa_id_alumno_en_pivot(): void
    {
        DB::table('alumnos')->insert(['nia' => '10002394']);
        DB::table('reuniones')->insert([
            'id' => 1,
            'tipo' => 7,
            'numero' => 35,
            'idProfesor' => 'P100',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $alumno = new Alumno();
        $alumno->nia = '10002394';

        $grupo = new Grupo();
        $grupo->codigo = 'G1';
        $grupo->curso = 1;
        $grupo->setRelation('Alumnos', new EloquentCollection([
            $alumno,
        ]));

        $grupoService = $this->createMock(GrupoService::class);
        $grupoService->method('largestByTutor')->with('P100')->willReturn($grupo);
        app()->instance(GrupoService::class, $grupoService);

        $listener = new AsistentesCreate($this->createMock(ProfesorService::class));
        $reunion = Reunion::query()->findOrFail(1);

        $this->callProtectedMethod($listener, 'assignaAlumnes', [$reunion]);
        $this->callProtectedMethod($listener, 'assignaAlumnes', [$reunion]);

        $this->assertSame(1, DB::table('alumno_reuniones')
            ->where('idReunion', 1)
            ->where('idAlumno', '10002394')
            ->where('capacitats', 3)
            ->count());
    }
}
