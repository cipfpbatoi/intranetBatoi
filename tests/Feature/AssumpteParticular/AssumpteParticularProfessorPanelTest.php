<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\AssumpteParticular\TornAssumpteParticularService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use Intranet\Livewire\AssumpteParticularProfessorPanel;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

/**
 * Proves del panell d'assumptes particulars del professorat.
 */
class AssumpteParticularProfessorPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-01 09:00:00');
        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'assumptes-panel-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->crearEsquema();
        $this->crearProfesor('PROF001');
        $this->crearProfesor('PROF002');

        $torns = Mockery::mock(TornAssumpteParticularService::class);
        $torns->shouldReceive('delProfessor')
            ->zeroOrMoreTimes()
            ->andReturn(AssumpteParticular::TORN_MATI);
        app()->instance(TornAssumpteParticularService::class, $torns);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        DB::disconnect('sqlite');
        if ($this->sqlitePath !== ':memory:' && file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_mostra_saldos_i_nomes_les_peticions_propies(): void
    {
        $this->crearPeticio('PROF001', '2026-10-15');
        $this->crearPeticio(
            'PROF002',
            '2026-10-16',
            AssumpteParticular::ESTAT_DENEGADA,
            'Resolució aliena'
        );

        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->assertSee('Assumptes particulars')
            ->assertSee('15/10/2026')
            ->assertSee('1 peticions pendents reservades')
            ->assertDontSee('16/10/2026')
            ->assertDontSee('Resolució aliena');
    }

    public function test_previsualitza_i_confirma_una_peticio_valida(): void
    {
        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->set('dataGaudi', '2026-10-15')
            ->set('plaActivitats', 'Tasques preparades per als grups afectats.')
            ->call('previsualitzar')
            ->assertSet('error', '')
            ->assertSet('previsualitzacio.tipus', AssumpteParticular::TIPUS_LECTIU)
            ->assertSee('Confirmar i enviar')
            ->call('enviar')
            ->assertSet('previsualitzacio', null)
            ->assertSet('missatge', 'La sol·licitud s’ha registrat i queda pendent de resolució.');

        $peticio = AssumpteParticular::query()->where('idProfesor', 'PROF001')->firstOrFail();
        $this->assertSame('2026-10-15', $peticio->data_gaudi->toDateString());
        $this->assertSame(AssumpteParticular::ESTAT_PENDENT, $peticio->estat);
    }

    public function test_rebutja_una_peticio_urgent_sense_motivacio(): void
    {
        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->set('dataGaudi', '2026-10-05')
            ->set('plaActivitats', 'Pla preparat.')
            ->call('previsualitzar')
            ->assertSet('previsualitzacio', null)
            ->assertSee('Cal motivar');

        $this->assertDatabaseCount('assumptes_particulars', 0);
    }

    public function test_rebutja_un_dia_lectiu_sense_pla_d_activitats(): void
    {
        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->set('dataGaudi', '2026-10-15')
            ->call('previsualitzar')
            ->assertSet('previsualitzacio', null)
            ->assertSee('pla d’activitats és obligatori');

        $this->assertDatabaseCount('assumptes_particulars', 0);
    }

    public function test_pot_cancel_lar_una_peticio_propia_pendent(): void
    {
        $peticio = $this->crearPeticio('PROF001', '2026-10-15');

        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->call('cancelLarPeticio', $peticio->id)
            ->assertSet('error', '')
            ->assertSet('missatge', 'La sol·licitud s’ha cancel·lat correctament.');

        $this->assertDatabaseHas('assumptes_particulars', [
            'id' => $peticio->id,
            'estat' => AssumpteParticular::ESTAT_CANCEL_LADA,
        ]);
    }

    public function test_no_pot_cancel_lar_una_peticio_aliena_ni_una_resolta(): void
    {
        $aliena = $this->crearPeticio('PROF002', '2026-10-15');
        $resolta = $this->crearPeticio(
            'PROF001',
            '2026-10-16',
            AssumpteParticular::ESTAT_AUTORITZADA
        );

        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->assertDontSeeHtml('wire:click="cancelLarPeticio(' . $resolta->id . ')"')
            ->call('cancelLarPeticio', $aliena->id)
            ->assertForbidden();

        Livewire::actingAs($this->professor('PROF001'), 'profesor')
            ->test(AssumpteParticularProfessorPanel::class)
            ->call('cancelLarPeticio', $resolta->id)
            ->assertForbidden();
    }

    private function professor(string $dni): Profesor
    {
        return Profesor::on('sqlite')->findOrFail($dni);
    }

    private function crearEsquema(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->unsignedBigInteger('rol')->default(config('roles.rol.profesor'));
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

    private function crearProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Professor',
            'apellido1' => $dni,
            'email' => strtolower($dni) . '@test.local',
            'fecha_ingreso' => '2026-09-01',
            'rol' => config('roles.rol.profesor'),
            'activo' => true,
        ]);
    }

    private function crearPeticio(
        string $dni,
        string $data,
        string $estat = AssumpteParticular::ESTAT_PENDENT,
        ?string $resolucio = null
    ): AssumpteParticular {
        return AssumpteParticular::query()->create([
            'idProfesor' => $dni,
            'data_gaudi' => $data,
            'curs' => '2026-2027',
            'tipus' => AssumpteParticular::TIPUS_LECTIU,
            'torn' => AssumpteParticular::TORN_MATI,
            'estat' => $estat,
            'pla_activitats' => 'Pla',
            'resolucio' => $resolucio,
        ]);
    }
}
