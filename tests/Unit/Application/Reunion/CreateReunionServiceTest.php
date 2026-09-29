<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Reunion\CreateReunionData;
use Intranet\Application\Reunion\CreateReunionService;
use Intranet\Application\Reunion\ReunionActNumberService;
use Intranet\Application\Reunion\ReunionFeValuationService;
use Intranet\Application\Reunion\ReunionOrderGenerateService;
use Intranet\Application\Reunion\ReunionParticipantAssigner;
use Intranet\Application\Reunion\ReunionCreationGroupResolver;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use RuntimeException;
use Tests\TestCase;

/**
 * Proves del cas d'ús transaccional de creació de reunions.
 */
class CreateReunionServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Event::fake();

        Schema::create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor', 10)->nullable();
            $table->unsignedTinyInteger('curso')->nullable();
            $table->timestamps();
        });
        Schema::create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo');
            $table->string('grupo')->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->string('curso', 20);
            $table->string('organo_acta', 100)->nullable();
            $table->unsignedInteger('numero_acta')->nullable();
            $table->unsignedTinyInteger('numero')->nullable();
            $table->dateTime('fecha');
            $table->string('descripcion', 120);
            $table->text('objetivos')->nullable();
            $table->string('idProfesor', 10);
            $table->string('idEspacio', 10);
            $table->boolean('archivada')->default(false);
            $table->string('fichero')->nullable();
            $table->timestamps();
        });
        Schema::create('reunion_acta_counters', function (Blueprint $table): void {
            $table->string('curso', 20);
            $table->string('organo', 100);
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->primary(['curso', 'organo']);
        });
        Schema::create('asistencias', function (Blueprint $table): void {
            $table->unsignedInteger('idReunion');
            $table->string('idProfesor', 10);
            $table->boolean('asiste')->default(false);
        });
        Schema::create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedTinyInteger('orden');
            $table->string('descripcion');
        });
    }

    public function test_crea_tota_la_reunio_amb_convocant_i_grup_explicits(): void
    {
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => '1r DAM (LFP)',
            'tutor' => 'P1',
            'curso' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupService = $this->createMock(GrupoService::class);
        $groupService->expects($this->once())
            ->method('largestByTutor')
            ->with('P1')
            ->willReturn(Grupo::query()->findOrFail('G1'));

        $participants = $this->createMock(ReunionParticipantAssigner::class);
        $participants->expects($this->once())
            ->method('assign')
            ->with($this->isInstanceOf(Reunion::class), $this->isInstanceOf(Profesor::class));

        $orders = $this->createMock(ReunionOrderGenerateService::class);
        $orders->expects($this->once())->method('generate');

        $fe = $this->createMock(ReunionFeValuationService::class);
        $fe->expects($this->once())->method('ensureOrder')->with(
            $this->isInstanceOf(Reunion::class),
            'LFP'
        );

        $reunion = (new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $participants,
            $orders,
            $fe,
            new ReunionActNumberService()
        ))
            ->create($this->data(), $this->creator());

        $this->assertSame('P1', $reunion->idProfesor);
        $this->assertSame('G1', $reunion->idGrupo);
        $this->assertSame(1, $reunion->numero_acta);
        $this->assertNull($reunion->numero);
        $this->assertDatabaseHas('reuniones', [
            'id' => $reunion->id,
            'idProfesor' => 'P1',
            'idGrupo' => 'G1',
            'descripcion' => 'Reunió completa',
        ]);
    }

    public function test_elimina_el_grup_accidental_en_un_col_lectiu_no_docent(): void
    {
        $groupService = $this->createMock(GrupoService::class);
        $participants = $this->createMock(ReunionParticipantAssigner::class);
        $orders = $this->createMock(ReunionOrderGenerateService::class);
        $fe = $this->createMock(ReunionFeValuationService::class);

        $data = new CreateReunionData(
            1,
            null,
            'G-INJECTAT',
            '2026-2027',
            1,
            '2026-10-01 10:00:00',
            'Claustre',
            null,
            'A101'
        );

        $reunion = (new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $participants,
            $orders,
            $fe,
            new ReunionActNumberService()
        ))
            ->create($data, $this->creator());

        $this->assertNull($reunion->idGrupo);
        $this->assertNull($reunion->numero);
    }

    public function test_conserva_el_numero_funcional_de_l_avaluacio(): void
    {
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => '1r DAM (LFP)',
            'tutor' => 'P1',
            'curso' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupService = $this->createMock(GrupoService::class);
        $groupService->method('largestByTutor')->willReturn(Grupo::query()->findOrFail('G1'));
        $service = new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $this->createMock(ReunionParticipantAssigner::class),
            $this->createMock(ReunionOrderGenerateService::class),
            $this->createMock(ReunionFeValuationService::class),
            new ReunionActNumberService()
        );
        $data = new CreateReunionData(
            7,
            null,
            null,
            '2026-2027',
            34,
            '2026-10-01 10:00:00',
            'Avaluació final',
            null,
            'A101'
        );

        $reunion = $service->create($data, $this->creator());

        $this->assertSame(34, $reunion->numero);
        $this->assertSame(1, $reunion->numero_acta);
    }

    public function test_desfa_tota_la_creacio_si_falla_un_pas(): void
    {
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => '1r DAM (LFP)',
            'tutor' => 'P1',
            'curso' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupService = $this->createMock(GrupoService::class);
        $groupService->method('largestByTutor')->willReturn(Grupo::query()->findOrFail('G1'));

        $participants = $this->createMock(ReunionParticipantAssigner::class);
        $participants->method('assign')->willReturnCallback(static function (Reunion $reunion): void {
            DB::table('asistencias')->insert([
                'idReunion' => $reunion->id,
                'idProfesor' => 'P1',
                'asiste' => true,
            ]);
        });

        $orders = $this->createMock(ReunionOrderGenerateService::class);
        $orders->method('generate')->willReturnCallback(static function (Reunion $reunion): void {
            DB::table('ordenes_reuniones')->insert([
                'idReunion' => $reunion->id,
                'orden' => 1,
                'descripcion' => 'Punt parcial',
            ]);
        });

        $fe = $this->createMock(ReunionFeValuationService::class);
        $fe->method('ensureOrder')->willThrowException(new RuntimeException('Error de FE'));

        $service = new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $participants,
            $orders,
            $fe,
            new ReunionActNumberService()
        );

        try {
            $service->create($this->data(), $this->creator());
            $this->fail('La fallada del procés havia de propagar-se.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Error de FE', $exception->getMessage());
        }

        $this->assertDatabaseCount('reuniones', 0);
        $this->assertDatabaseCount('reunion_acta_counters', 0);
        $this->assertDatabaseCount('asistencias', 0);
        $this->assertDatabaseCount('ordenes_reuniones', 0);
    }

    public function test_rebutja_un_grup_que_el_professor_no_tutoritza(): void
    {
        DB::table('grupos')->insert([
            [
                'codigo' => 'G1',
                'nombre' => 'Grup propi (LFP)',
                'tutor' => 'P1',
                'curso' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'G2',
                'nombre' => 'Grup alié (LFP)',
                'tutor' => 'P2',
                'curso' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $groupService = $this->createMock(GrupoService::class);
        $groupService->method('qTutor')->with('P1')->willReturn(new EloquentCollection([
            Grupo::query()->findOrFail('G1'),
        ]));
        $service = new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $this->createMock(ReunionParticipantAssigner::class),
            $this->createMock(ReunionOrderGenerateService::class),
            $this->createMock(ReunionFeValuationService::class),
            new ReunionActNumberService()
        );

        try {
            $service->create($this->data('G2'), $this->creator());
            $this->fail("S'havia de rebutjar el grup alié.");
        } catch (AuthorizationException $exception) {
            $this->assertSame(
                'Només pots crear actes per als grups que tutoritzes.',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseCount('reuniones', 0);
    }

    public function test_direccio_pot_crear_per_qualsevol_grup_existent(): void
    {
        DB::table('grupos')->insert([
            'codigo' => 'G2',
            'nombre' => 'Grup institucional (LFP)',
            'tutor' => 'P2',
            'curso' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupService = $this->createMock(GrupoService::class);
        $groupService->method('find')->with('G2')->willReturn(Grupo::query()->findOrFail('G2'));
        $service = new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $this->createMock(ReunionParticipantAssigner::class),
            $this->createMock(ReunionOrderGenerateService::class),
            $this->createMock(ReunionFeValuationService::class),
            new ReunionActNumberService()
        );

        $reunion = $service->create(
            $this->data('G2'),
            $this->creator((int) config('roles.rol.direccion'))
        );

        $this->assertSame('G2', $reunion->idGrupo);
    }

    public function test_professor_sense_tutoria_no_pot_crear_una_reunio_de_grup(): void
    {
        $groupService = $this->createMock(GrupoService::class);
        $groupService->method('largestByTutor')->with('P1')->willReturn(null);
        $service = new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $this->createMock(ReunionParticipantAssigner::class),
            $this->createMock(ReunionOrderGenerateService::class),
            $this->createMock(ReunionFeValuationService::class),
            new ReunionActNumberService()
        );

        try {
            $service->create($this->data(), $this->creator());
            $this->fail("S'havia de requerir un grup tutoritzat.");
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('idGrupo', $exception->errors());
        }

        $this->assertDatabaseCount('reuniones', 0);
    }

    public function test_direccio_no_pot_crear_per_un_grup_inexistent(): void
    {
        $groupService = $this->createMock(GrupoService::class);
        $groupService->method('find')->with('NO-EXISTIX')->willReturn(null);
        $service = new CreateReunionService(
            new ReunionCreationGroupResolver($groupService),
            $this->createMock(ReunionParticipantAssigner::class),
            $this->createMock(ReunionOrderGenerateService::class),
            $this->createMock(ReunionFeValuationService::class),
            new ReunionActNumberService()
        );

        try {
            $service->create(
                $this->data('NO-EXISTIX'),
                $this->creator((int) config('roles.rol.direccion'))
            );
            $this->fail("S'havia de rebutjar el grup inexistent.");
        } catch (ValidationException $exception) {
            $this->assertSame(
                ['El grup docent seleccionat no existeix.'],
                $exception->errors()['idGrupo']
            );
        }

        $this->assertDatabaseCount('reuniones', 0);
    }

    private function data(?string $idGrupo = null): CreateReunionData
    {
        return new CreateReunionData(
            2,
            null,
            $idGrupo,
            '2026-2027',
            1,
            '2026-10-01 10:00:00',
            'Reunió completa',
            'Objectius',
            'A101'
        );
    }

    private function creator(int $role = 3): Profesor
    {
        $creator = new Profesor();
        $creator->dni = 'P1';
        $creator->departamento = 10;
        $creator->rol = $role;

        return $creator;
    }
}
