<?php

namespace Tests\Unit\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Actividad;
use Intranet\Services\School\ActividadParticipantsService;
use Tests\TestCase;

class ActividadParticipantsServiceTest extends TestCase
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

    public function test_add_profesor_no_duplica_pivot(): void
    {
        $actividadId = $this->seedActividad();
        $this->seedProfesor('P100');

        DB::table('actividad_profesor')->insert([
            'idActividad' => $actividadId,
            'idProfesor' => 'P100',
            'coordinador' => 1,
        ]);

        (new ActividadParticipantsService())->addProfesor($actividadId, 'P100');

        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('idProfesor', 'P100')
            ->count());
    }

    public function test_add_group_i_remove_group_actualitzen_pivot(): void
    {
        $actividadId = $this->seedActividad();
        DB::table('grupos')->insert(['codigo' => 'G100', 'nombre' => 'Grup 100']);

        $service = new ActividadParticipantsService();
        $service->addGroup($actividadId, 'G100');
        $this->assertSame(1, DB::table('actividad_grupo')
            ->where('idActividad', $actividadId)
            ->where('idGrupo', 'G100')
            ->count());

        $service->removeGroup($actividadId, 'G100');
        $this->assertSame(0, DB::table('actividad_grupo')
            ->where('idActividad', $actividadId)
            ->where('idGrupo', 'G100')
            ->count());
    }

    public function test_assign_coordinator_retornar_false_si_no_participa(): void
    {
        $actividadId = $this->seedActividad();
        $this->seedProfesor('P200');
        $this->seedProfesor('P201');

        DB::table('actividad_profesor')->insert([
            'idActividad' => $actividadId,
            'idProfesor' => 'P200',
            'coordinador' => 1,
        ]);

        $result = (new ActividadParticipantsService())->assignCoordinator($actividadId, 'P201');

        $this->assertFalse($result);
        $this->assertSame(1, (int) DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('idProfesor', 'P200')
            ->value('coordinador'));
    }

    public function test_assign_coordinator_deixa_unic_responsable(): void
    {
        $actividadId = $this->seedActividad();
        $this->seedProfesor('P300');
        $this->seedProfesor('P301');

        DB::table('actividad_profesor')->insert([
            ['idActividad' => $actividadId, 'idProfesor' => 'P300', 'coordinador' => 1],
            ['idActividad' => $actividadId, 'idProfesor' => 'P301', 'coordinador' => 0],
        ]);

        $result = (new ActividadParticipantsService())->assignCoordinator($actividadId, 'P301');

        $this->assertTrue($result);
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('coordinador', 1)
            ->count());
        $this->assertSame(1, (int) DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('idProfesor', 'P301')
            ->value('coordinador'));
    }

    public function test_remove_profesor_retornar_false_si_es_lultim(): void
    {
        $actividadId = $this->seedActividad();
        $this->seedProfesor('P400');

        DB::table('actividad_profesor')->insert([
            'idActividad' => $actividadId,
            'idProfesor' => 'P400',
            'coordinador' => 1,
        ]);

        $result = (new ActividadParticipantsService())->removeProfesor($actividadId, 'P400');

        $this->assertFalse($result);
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->count());
    }

    public function test_remove_profesor_coordinador_reassigna_un_altre(): void
    {
        $actividadId = $this->seedActividad();
        $this->seedProfesor('P500');
        $this->seedProfesor('P501');

        DB::table('actividad_profesor')->insert([
            ['idActividad' => $actividadId, 'idProfesor' => 'P500', 'coordinador' => 1],
            ['idActividad' => $actividadId, 'idProfesor' => 'P501', 'coordinador' => 0],
        ]);

        $result = (new ActividadParticipantsService())->removeProfesor($actividadId, 'P500');

        $this->assertTrue($result);
        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->count());
        $this->assertSame('P501', DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('coordinador', 1)
            ->value('idProfesor'));
    }

    public function test_metodes_llancen_error_si_actividad_no_existix(): void
    {
        $service = new ActividadParticipantsService();

        $this->expectException(ModelNotFoundException::class);
        $service->addGroup(999999, 'G404');
    }

    public function test_assign_initial_participants_assigna_coordinador_i_grup(): void
    {
        $actividadId = $this->seedActividad();
        $this->seedProfesor('P600');

        DB::table('grupos')->insert([
            'codigo' => 'G600',
            'nombre' => 'Grup 600',
            'tutor' => 'P600',
        ]);

        $actividad = Actividad::findOrFail($actividadId);
        (new ActividadParticipantsService())->assignInitialParticipants($actividad, 'P600');

        $this->assertSame(1, DB::table('actividad_profesor')
            ->where('idActividad', $actividadId)
            ->where('idProfesor', 'P600')
            ->where('coordinador', 1)
            ->count());

        $this->assertSame(1, DB::table('actividad_grupo')
            ->where('idActividad', $actividadId)
            ->where('idGrupo', 'G600')
            ->count());
    }

    private function seedActividad(): int
    {
        return (int) DB::table('actividades')->insertGetId([
            'name' => 'Activitat test',
            'extraescolar' => 1,
            'estado' => 0,
        ]);
    }

    private function seedProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('actividad_grupo');
        $schema->dropIfExists('actividad_profesor');
        $schema->dropIfExists('alumnos_grupos');
        $schema->dropIfExists('alumnos');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('profesores');
        $schema->dropIfExists('actividades');

        $schema->create('actividades', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->unsignedTinyInteger('extraescolar')->default(1);
            $table->unsignedTinyInteger('estado')->default(0);
        });

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor')->nullable();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });

        $schema->create('actividad_profesor', function (Blueprint $table): void {
            $table->unsignedInteger('idActividad');
            $table->string('idProfesor');
            $table->unsignedTinyInteger('coordinador')->default(0);
        });

        $schema->create('actividad_grupo', function (Blueprint $table): void {
            $table->unsignedInteger('idActividad');
            $table->string('idGrupo');
        });
    }
}
