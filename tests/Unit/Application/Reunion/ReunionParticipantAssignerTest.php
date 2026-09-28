<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Application\Reunion\ReunionParticipantAssigner;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Tests\TestCase;

/**
 * Proves de caracterització dels participants de cada col·lectiu.
 */
class ReunionParticipantAssignerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Event::fake();

        $this->createSchema();
    }

    public function test_assigna_els_cinc_col_lectius_sense_guard_ni_duplicats(): void
    {
        DB::table('grupos')->insert(['codigo' => 'G1', 'curso' => 1]);

        $departmentTeacher = $this->teacher('DEP1');
        $staffTeacher = $this->teacher('CLA1');
        $groupTeacher = $this->teacher('GRP1');
        $workGroupTeacher = $this->teacher('TRE1');
        $headTeacher = $this->teacher('CAP1', (int) config('roles.rol.jefe_dpto'));

        $teachers = $this->createMock(ProfesorService::class);
        $teachers->method('plantillaByDepartamento')->with('10')
            ->willReturn(new EloquentCollection([$departmentTeacher]));
        $teachers->method('plantilla')->willReturn(new EloquentCollection([$staffTeacher]));
        $teachers->method('byGrupo')->with('G1')->willReturn(new EloquentCollection([$groupTeacher]));
        $teachers->method('byGrupoTrabajo')->with('GT1')
            ->willReturn(new EloquentCollection([$workGroupTeacher]));
        $teachers->method('activos')->willReturn(new EloquentCollection([
            $headTeacher,
            $this->teacher('PRO1', (int) config('roles.rol.profesor')),
        ]));

        $assigner = new ReunionParticipantAssigner($teachers);
        $creator = $this->teacher('P1');
        $creator->departamento = 10;

        $meetings = [
            $this->meeting(1, 0),
            $this->meeting(2, 1),
            $this->meeting(3, 2, idGroup: 'G1'),
            $this->meeting(4, 3, workGroup: 'GT1'),
            $this->meeting(5, 8),
        ];

        foreach ($meetings as $meeting) {
            $assigner->assign($meeting, $creator);
            $assigner->assign($meeting, $creator);
        }

        $this->assertSame([
            '1:DEP1',
            '2:CLA1',
            '3:GRP1',
            '4:TRE1',
            '5:CAP1',
        ], DB::table('asistencias')
            ->orderBy('idReunion')
            ->get()
            ->map(static fn ($row): string => $row->idReunion . ':' . $row->idProfesor)
            ->all());
    }

    public function test_assigna_alumnat_en_l_avaluacio_extraordinaria(): void
    {
        DB::table('grupos')->insert(['codigo' => 'G1', 'curso' => 1]);
        DB::table('alumnos')->insert([['nia' => 'A1'], ['nia' => 'A2']]);
        DB::table('alumnos_grupos')->insert([
            ['idGrupo' => 'G1', 'idAlumno' => 'A1'],
            ['idGrupo' => 'G1', 'idAlumno' => 'A2'],
        ]);

        $teachers = $this->createMock(ProfesorService::class);
        $teachers->method('byGrupo')->willReturn(new EloquentCollection());
        $assigner = new ReunionParticipantAssigner($teachers);
        $meeting = $this->meeting(1, 7, 35, 'G1');

        $assigner->assign($meeting, $this->teacher('P1'));
        $assigner->assign($meeting, $this->teacher('P1'));

        $this->assertSame(2, DB::table('alumno_reuniones')->count());
        $this->assertSame(2, DB::table('alumno_reuniones')->where('capacitats', 3)->count());
    }

    public function test_crear_el_model_directament_no_assigna_participants(): void
    {
        Reunion::query()->create([
            'tipo' => 1,
            'idProfesor' => 'P1',
        ]);

        $this->assertDatabaseCount('asistencias', 0);
    }

    private function createSchema(): void
    {
        Schema::create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->unsignedTinyInteger('curso')->nullable();
        });
        Schema::create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo');
            $table->unsignedTinyInteger('numero')->nullable();
            $table->string('grupo')->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->string('idProfesor', 10);
            $table->timestamps();
        });
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
        });
        Schema::create('asistencias', function (Blueprint $table): void {
            $table->unsignedInteger('idReunion');
            $table->string('idProfesor', 10);
            $table->boolean('asiste')->default(false);
            $table->unique(['idReunion', 'idProfesor']);
        });
        Schema::create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 15)->primary();
        });
        Schema::create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idGrupo', 10);
            $table->string('idAlumno', 15);
        });
        Schema::create('alumno_reuniones', function (Blueprint $table): void {
            $table->unsignedInteger('idReunion');
            $table->string('idAlumno', 15);
            $table->unsignedTinyInteger('capacitats')->default(0);
            $table->unique(['idReunion', 'idAlumno']);
        });
    }

    private function teacher(string $dni, int $role = 3): Profesor
    {
        $teacher = new Profesor();
        $teacher->dni = $dni;
        $teacher->rol = $role;
        $teacher->fecha_baja = null;
        $teacher->sustituye_a = null;

        return $teacher;
    }

    private function meeting(
        int $id,
        int $type,
        ?int $number = null,
        ?string $idGroup = null,
        ?string $workGroup = null
    ): Reunion {
        DB::table('reuniones')->insert([
            'id' => $id,
            'tipo' => $type,
            'numero' => $number,
            'grupo' => $workGroup,
            'idGrupo' => $idGroup,
            'idProfesor' => 'P1',
        ]);

        return Reunion::query()->findOrFail($id);
    }
}
