<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\AssumpteParticularService;
use Intranet\Application\AssumpteParticular\CalendariAssumpteParticularService;
use Intranet\Application\AssumpteParticular\ContingentAssumpteParticularCalculator;
use Intranet\Application\AssumpteParticular\SaldoAssumpteParticularCalculator;
use Intranet\Application\AssumpteParticular\TornAssumpteParticularService;
use Intranet\Entities\AssumpteParticular;
use Mockery;
use Tests\TestCase;

/**
 * Proves d'integració del servei d'assumptes particulars.
 */
class AssumpteParticularServiceTest extends TestCase
{
    private string $sqlitePath;
    private AssumpteParticularService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'assumptes-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->crearEsquema();
        $this->omplirCalendari();

        $torns = Mockery::mock(TornAssumpteParticularService::class);
        $torns->shouldReceive('delProfessor')->andReturn(AssumpteParticular::TORN_MATI);
        $torns->shouldReceive('plantillaPerTorn')->andReturn([
            AssumpteParticular::TORN_MATI => 100,
        ]);
        $this->service = new AssumpteParticularService(
            new SaldoAssumpteParticularCalculator(),
            new ContingentAssumpteParticularCalculator(),
            new CalendariAssumpteParticularService(),
            $torns
        );
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        if ($this->sqlitePath !== ':memory:' && file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }
        parent::tearDown();
    }

    public function test_una_peticio_valida_queda_pendent_i_no_genera_falta(): void
    {
        $this->crearProfesor('PROF001');

        $peticio = $this->service->crear(
            'PROF001',
            '2026-10-15',
            null,
            'Activitats preparades per als grups.',
            '2026-10-01'
        );

        $this->assertSame(AssumpteParticular::ESTAT_PENDENT, $peticio->estat);
        $this->assertSame(AssumpteParticular::TIPUS_LECTIU, $peticio->tipus);
        $this->assertNull($peticio->falta_id);
    }

    public function test_menys_de_set_dies_requerix_motivacio_excepcional(): void
    {
        $this->crearProfesor('PROF001');

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('Cal motivar');
        $this->service->crear('PROF001', '2026-10-05', null, 'Pla', '2026-10-01');
    }

    public function test_una_avaluacio_del_calendari_es_rebutja_amb_motiu_especific(): void
    {
        $this->crearProfesor('PROF001');
        DB::table('calendari_escolar')->where('data', '2026-10-15')->update([
            'esdeveniment' => 'Primera avaluació',
        ]);

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('avaluació');
        $this->service->crear('PROF001', '2026-10-15', null, 'Pla', '2026-10-01');
    }

    public function test_divendres_i_dilluns_es_consideren_lectius_consecutius(): void
    {
        $this->crearProfesor('PROF001');
        $this->crearPeticioAutoritzada('PROF001', '2026-10-16');

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('consecutius');
        $this->service->crear('PROF001', '2026-10-19', null, 'Pla', '2026-10-05');
    }

    public function test_denegades_i_cancel_lades_no_consumixen_saldo(): void
    {
        $this->crearProfesor('PROF001');
        $this->crearPeticio('PROF001', '2026-10-13', AssumpteParticular::ESTAT_DENEGADA);
        $this->crearPeticio('PROF001', '2026-10-14', AssumpteParticular::ESTAT_CANCEL_LADA);

        $this->assertSame(3.0, $this->service->saldo(
            'PROF001',
            '2026-2027',
            AssumpteParticular::TIPUS_LECTIU
        ));
    }

    public function test_nomes_autoritza_huit_peticions_del_mateix_dia(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            $dni = sprintf('PROF%03d', $i);
            $this->crearProfesor($dni);
            $peticio = $this->crearPeticio($dni, '2026-10-15');
            if ($i <= 8) {
                $this->service->autoritzar($peticio->id, 'DIRE001');
            }
        }

        $novena = AssumpteParticular::query()->where('idProfesor', 'PROF009')->firstOrFail();
        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('màxim de huit');
        $this->service->autoritzar($novena->id, 'DIRE001');
    }

    private function crearEsquema(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->unsignedBigInteger('rol')->default(3);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
        Schema::create('calendari_escolar', function (Blueprint $table): void {
            $table->id();
            $table->date('data')->unique();
            $table->string('tipus');
            $table->string('esdeveniment')->nullable();
            $table->timestamps();
        });
        Schema::create('assumptes_particulars', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor');
            $table->date('data_gaudi');
            $table->string('curs');
            $table->string('tipus');
            $table->string('torn');
            $table->string('estat')->default(AssumpteParticular::ESTAT_PENDENT);
            $table->text('motivacio_excepcional')->nullable();
            $table->text('pla_activitats')->nullable();
            $table->text('resolucio')->nullable();
            $table->timestamp('sollicitada_at')->nullable();
            $table->timestamp('resolta_at')->nullable();
            $table->timestamp('cancel_lada_at')->nullable();
            $table->string('resolta_per')->nullable();
            $table->unsignedInteger('falta_id')->nullable();
            $table->timestamps();
        });
    }

    private function omplirCalendari(): void
    {
        $dia = CarbonImmutable::parse('2026-09-01');
        $fi = CarbonImmutable::parse('2027-07-31');
        $files = [];
        while ($dia->lessThanOrEqualTo($fi)) {
            $files[] = [
                'data' => $dia->toDateString(),
                'tipus' => $dia->isWeekend() ? 'festiu' : 'lectiu',
                'esdeveniment' => null,
            ];
            $dia = $dia->addDay();
        }
        foreach (array_chunk($files, 100) as $bloc) {
            DB::table('calendari_escolar')->insert($bloc);
        }
    }

    private function crearProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'fecha_ingreso' => '2026-09-01',
            'fecha_baja' => null,
            'rol' => 3,
            'activo' => true,
        ]);
    }

    private function crearPeticio(
        string $dni,
        string $data,
        string $estat = AssumpteParticular::ESTAT_PENDENT
    ): AssumpteParticular {
        return AssumpteParticular::query()->create([
            'idProfesor' => $dni,
            'data_gaudi' => $data,
            'curs' => '2026-2027',
            'tipus' => AssumpteParticular::TIPUS_LECTIU,
            'torn' => AssumpteParticular::TORN_MATI,
            'estat' => $estat,
            'pla_activitats' => 'Pla',
        ]);
    }

    private function crearPeticioAutoritzada(string $dni, string $data): AssumpteParticular
    {
        return $this->crearPeticio($dni, $data, AssumpteParticular::ESTAT_AUTORITZADA);
    }
}
