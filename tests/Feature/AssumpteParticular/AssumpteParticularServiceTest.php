<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\AssumpteParticularService;
use Intranet\Application\AssumpteParticular\CalendariAssumpteParticularService;
use Intranet\Application\AssumpteParticular\ContingentAssumpteParticularCalculator;
use Intranet\Application\AssumpteParticular\RubricaAssumpteParticularService;
use Intranet\Application\AssumpteParticular\SaldoAssumpteParticularCalculator;
use Intranet\Application\AssumpteParticular\TornAssumpteParticularService;
use Intranet\Application\Falta\FaltaService;
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
        Event::fake();
        Storage::fake('local');
        Storage::fake('public');
        $this->crearEsquema();

        $torns = Mockery::mock(TornAssumpteParticularService::class);
        $torns->shouldReceive('delProfessor')->andReturn(AssumpteParticular::TORN_MATI);
        $torns->shouldReceive('plantillaPerTorn')->andReturn([
            AssumpteParticular::TORN_MATI => 100,
        ]);
        $this->service = new AssumpteParticularService(
            new SaldoAssumpteParticularCalculator(),
            new ContingentAssumpteParticularCalculator(),
            new CalendariAssumpteParticularService(),
            $torns,
            new FaltaService(),
            new RubricaAssumpteParticularService()
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

    public function test_un_laborable_sense_registre_es_lectiu(): void
    {
        $this->crearProfesor('PROF001');

        $peticio = $this->service->crear(
            'PROF001',
            '2026-10-15',
            null,
            'Pla',
            '2026-10-01'
        );

        $this->assertSame(AssumpteParticular::TIPUS_LECTIU, $peticio->tipus);
    }

    public function test_un_cap_de_setmana_sense_registre_es_rebutja(): void
    {
        $this->crearProfesor('PROF001');

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('cap de setmana');
        $this->service->crear('PROF001', '2026-10-17', null, null, '2026-10-01');
    }

    public function test_un_dia_marcat_com_no_lectiu_consumix_la_bossa_no_lectiva(): void
    {
        $this->crearProfesor('PROF001');
        $this->crearDiaCalendari('2026-10-15', 'no lectiu');

        $peticio = $this->service->crear('PROF001', '2026-10-15', null, null, '2026-10-01');

        $this->assertSame(AssumpteParticular::TIPUS_NO_LECTIU, $peticio->tipus);
    }

    public function test_un_dia_marcat_com_festiu_es_rebutja(): void
    {
        $this->crearProfesor('PROF001');
        $this->crearDiaCalendari('2026-10-15', 'festiu');

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('dia festiu');
        $this->service->crear('PROF001', '2026-10-15', null, null, '2026-10-01');
    }

    public function test_els_primers_dies_lectius_es_calculen_sense_registres(): void
    {
        $this->crearProfesor('PROF001');

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('set primers');
        $this->service->crear('PROF001', '2026-09-02', null, 'Pla', '2026-08-03');
    }

    public function test_la_proximitat_a_un_periode_exclos_usa_laborables_sense_registre(): void
    {
        $this->crearProfesor('PROF001');
        $this->crearDiaCalendari('2026-12-25', 'festiu', 'Nadal');

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('període exclòs');
        $this->service->crear('PROF001', '2026-12-16', null, 'Pla', '2026-11-20');
    }

    public function test_la_previsualitzacio_valida_sense_persistir(): void
    {
        $this->crearProfesor('PROF001');

        $resultat = $this->service->previsualitzar(
            'PROF001',
            '2026-10-15',
            null,
            'Activitats preparades per als grups.',
            '2026-10-01'
        );

        $this->assertSame(AssumpteParticular::TIPUS_LECTIU, $resultat['tipus']);
        $this->assertSame(3.0, $resultat['saldo']);
        $this->assertDatabaseCount('assumptes_particulars', 0);
    }

    public function test_les_pendents_es_mostren_com_reserva_sense_restar_saldo_legal(): void
    {
        $this->crearProfesor('PROF001');
        $this->crearPeticio('PROF001', '2026-10-15');

        $resum = $this->service->resumSaldo('PROF001', '2026-2027');

        $this->assertSame(3.0, $resum[AssumpteParticular::TIPUS_LECTIU]['disponible']);
        $this->assertSame(1, $resum[AssumpteParticular::TIPUS_LECTIU]['pendent']);
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
        $this->crearDiaCalendari('2026-10-15', 'lectiu', 'Primera avaluació');

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
                $document = sprintf('assumptes-particulars/resolucions/%d.pdf', $peticio->id);
                Storage::disk('local')->put($document, 'PDF firmat');
                $this->service->autoritzar($peticio->id, 'DIRE001', $document);
            }
        }

        $novena = AssumpteParticular::query()->where('idProfesor', 'PROF009')->firstOrFail();
        $document = sprintf('assumptes-particulars/resolucions/%d.pdf', $novena->id);
        Storage::disk('local')->put($document, 'PDF firmat');
        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('màxim de huit');
        $this->service->autoritzar($novena->id, 'DIRE001', $document);
    }

    public function test_autoritza_amb_una_unica_falta_i_el_document_firmat(): void
    {
        $this->crearProfesor('PROF001');
        $peticio = $this->crearPeticio('PROF001', '2026-10-15');
        $document = 'assumptes-particulars/resolucions/peticio-1.pdf';
        Storage::disk('local')->put($document, 'PDF firmat');

        $resolta = $this->service->autoritzar($peticio->id, 'DIRE001', $document);

        $this->assertSame(AssumpteParticular::ESTAT_AUTORITZADA, $resolta->estat);
        $this->assertNotNull($resolta->falta_id);
        $this->assertSame($document, $resolta->resolucio_document);
        $this->assertDatabaseHas('faltas', [
            'id' => $resolta->falta_id,
            'idProfesor' => 'PROF001',
            'desde' => '2026-10-15',
            'hasta' => '2026-10-15',
            'dia_completo' => 1,
            'estado' => 3,
        ]);
        $this->assertDatabaseCount('faltas', 1);
        Storage::disk('local')->assertExists($document);
    }

    public function test_no_autoritza_sense_document_firmat(): void
    {
        $this->crearProfesor('PROF001');
        $peticio = $this->crearPeticio('PROF001', '2026-10-15');

        try {
            $this->service->autoritzar($peticio->id, 'DIRE001', 'document-inexistent.pdf');
            $this->fail('S’esperava una excepció per falta de document firmat.');
        } catch (AssumpteParticularException $exception) {
            $this->assertStringContainsString('resolució oficial firmada', $exception->getMessage());
        }

        $this->assertSame(AssumpteParticular::ESTAT_PENDENT, $peticio->fresh()->estat);
        $this->assertDatabaseCount('faltas', 0);
    }

    public function test_una_resolucio_repetida_no_duplica_la_falta_i_neteja_el_document_orfe(): void
    {
        $this->crearProfesor('PROF001');
        $peticio = $this->crearPeticio('PROF001', '2026-10-15');
        Storage::disk('local')->put('resolucio-primera.pdf', 'PDF firmat');
        $this->service->autoritzar($peticio->id, 'DIRE001', 'resolucio-primera.pdf');
        Storage::disk('local')->put('resolucio-repetida.pdf', 'PDF firmat repetit');

        try {
            $this->service->autoritzar($peticio->id, 'DIRE001', 'resolucio-repetida.pdf');
            $this->fail('S’esperava una excepció en repetir la resolució.');
        } catch (AssumpteParticularException $exception) {
            $this->assertStringContainsString('petició pendent', $exception->getMessage());
        }

        $this->assertDatabaseCount('faltas', 1);
        Storage::disk('local')->assertExists('resolucio-primera.pdf');
        Storage::disk('local')->assertMissing('resolucio-repetida.pdf');
    }

    public function test_un_error_creant_la_falta_revertix_l_autoritzacio_i_neteja_el_document(): void
    {
        $this->crearProfesor('PROF001');
        $peticio = $this->crearPeticio('PROF001', '2026-10-15');
        $document = 'resolucio-amb-error.pdf';
        Storage::disk('local')->put($document, 'PDF firmat');
        $motiusOriginals = config('auxiliares.motivoAusencia');
        config(['auxiliares.motivoAusencia' => []]);

        try {
            $this->service->autoritzar($peticio->id, 'DIRE001', $document);
            $this->fail('S’esperava un error en crear la falta.');
        } catch (\LogicException $exception) {
            $this->assertStringContainsString('motiu de falta', $exception->getMessage());
        } finally {
            config(['auxiliares.motivoAusencia' => $motiusOriginals]);
        }

        $this->assertSame(AssumpteParticular::ESTAT_PENDENT, $peticio->fresh()->estat);
        $this->assertNull($peticio->fresh()->falta_id);
        $this->assertDatabaseCount('faltas', 0);
        Storage::disk('local')->assertMissing($document);
    }

    private function crearEsquema(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->unsignedBigInteger('rol')->default(3);
            $table->boolean('activo')->default(true);
            $table->string('foto')->nullable();
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
            $table->string('resolucio_document')->nullable();
            $table->timestamps();
        });
        Schema::create('faltas', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->date('desde');
            $table->date('hasta')->nullable();
            $table->string('motivos', 2);
            $table->string('observaciones', 200)->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->string('fichero', 100)->nullable();
            $table->time('hora_ini')->nullable();
            $table->time('hora_fin')->nullable();
            $table->boolean('dia_completo')->nullable();
            $table->boolean('baja')->nullable();
            $table->timestamps();
        });
    }

    private function crearDiaCalendari(
        string $data,
        string $tipus,
        ?string $esdeveniment = null
    ): void
    {
        DB::table('calendari_escolar')->insert([
            'data' => $data,
            'tipus' => $tipus,
            'esdeveniment' => $esdeveniment,
        ]);
    }

    private function crearProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'fecha_ingreso' => '2026-09-01',
            'fecha_baja' => null,
            'rol' => 3,
            'activo' => true,
            'foto' => $dni . '.png',
        ]);
        Storage::disk('public')->put('signatures/' . $dni . '.png', 'Rúbrica');
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
