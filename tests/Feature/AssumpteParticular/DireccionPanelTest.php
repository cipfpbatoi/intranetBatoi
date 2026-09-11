<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\AssumpteParticular\AssumpteParticularDireccionQueryService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use Intranet\Livewire\AssumpteParticularDireccionPanel;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Proves del panell diari d'assumptes particulars de Direcció.
 */
class DireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'assumptes-direccio-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->crearEsquema();
        $this->crearPlantilla();
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        if ($this->sqlitePath !== ':memory:' && file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_nomes_direccio_i_administracio_poden_obrir_el_component(): void
    {
        Livewire::actingAs($this->professor('DIR001'), 'profesor')
            ->test(AssumpteParticularDireccionPanel::class)
            ->assertOk();

        Livewire::actingAs($this->professor('ADM001'), 'profesor')
            ->test(AssumpteParticularDireccionPanel::class)
            ->assertOk();

        Livewire::actingAs($this->professor('PROF01'), 'profesor')
            ->test(AssumpteParticularDireccionPanel::class)
            ->assertForbidden();
    }

    public function test_ordena_per_dies_gaudits_hores_i_antiguitat(): void
    {
        $data = '2026-10-15';
        $this->crearPeticio('PROF01', '2026-10-01', AssumpteParticular::ESTAT_AUTORITZADA);
        $this->crearPeticio('PROF01', $data, AssumpteParticular::ESTAT_PENDENT, '2026-09-01 08:00:00');
        $this->crearPeticio('PROF02', $data, AssumpteParticular::ESTAT_PENDENT, '2026-09-01 08:00:00');
        $this->crearPeticio('PROF03', $data, AssumpteParticular::ESTAT_PENDENT, '2026-09-01 10:00:00');
        $this->crearPeticio('PROF04', $data, AssumpteParticular::ESTAT_PENDENT, '2026-09-01 09:00:00');

        $grups = app(AssumpteParticularDireccionQueryService::class)->grupsPendents();

        $this->assertCount(1, $grups);
        $this->assertSame(
            ['PROF04', 'PROF03', 'PROF02', 'PROF01'],
            array_column($grups[0]['peticions'], 'dni')
        );
        $this->assertSame([0, 0, 0, 1], array_column($grups[0]['peticions'], 'dies_gaudits'));
        $this->assertSame([1, 1, 2, 1], array_column($grups[0]['peticions'], 'hores_lectives'));
    }

    public function test_mostra_torn_ambdos_i_totes_les_hores_afectades(): void
    {
        $this->crearPeticio(
            'PROF02',
            '2026-10-15',
            AssumpteParticular::ESTAT_PENDENT,
            '2026-09-01 08:00:00',
            AssumpteParticular::TORN_AMBDOS
        );

        $peticio = app(AssumpteParticularDireccionQueryService::class)
            ->grupsPendents()[0]['peticions'][0];

        $this->assertSame(AssumpteParticular::TORN_AMBDOS, $peticio['torn_actual']);
        $this->assertSame(2, $peticio['hores_lectives']);
    }

    public function test_filtra_les_peticions_per_una_data_exacta_i_permet_netejar_el_filtre(): void
    {
        $this->crearPeticio('PROF01', '2026-10-15', AssumpteParticular::ESTAT_PENDENT);
        $this->crearPeticio('PROF02', '2026-10-16', AssumpteParticular::ESTAT_PENDENT);

        $grups = app(AssumpteParticularDireccionQueryService::class)
            ->grupsPendents('2026-10-16');

        $this->assertCount(1, $grups);
        $this->assertSame('2026-10-16', $grups[0]['data']);

        Livewire::actingAs($this->professor('DIR001'), 'profesor')
            ->test(AssumpteParticularDireccionPanel::class)
            ->set('filtreData', '2026-10-16')
            ->assertSee('16/10/2026')
            ->assertDontSee('15/10/2026')
            ->call('netejarFiltreData')
            ->assertSet('filtreData', '')
            ->assertSee('15/10/2026')
            ->assertSee('16/10/2026');
    }

    public function test_mostra_places_globals_i_per_torn(): void
    {
        $data = '2026-10-15';
        $this->crearPeticio('PROF05', $data, AssumpteParticular::ESTAT_AUTORITZADA);
        $this->crearPeticio('PROF01', $data, AssumpteParticular::ESTAT_PENDENT);

        $grup = app(AssumpteParticularDireccionQueryService::class)->grupsPendents()[0];

        $this->assertSame(8, $grup['quota_total']);
        $this->assertSame(1, $grup['autoritzades_total']);
        $this->assertSame(7, $grup['disponibles_total']);
        $this->assertSame(4, $grup['quotes'][AssumpteParticular::TORN_MATI]['quota']);
        $this->assertSame(1, $grup['quotes'][AssumpteParticular::TORN_MATI]['autoritzades']);
        $this->assertSame(3, $grup['quotes'][AssumpteParticular::TORN_MATI]['disponibles']);
    }

    public function test_avisa_de_canvis_de_calendari_torn_saldo_i_quota(): void
    {
        $data = '2026-10-15';
        DB::table('calendari_escolar')->insert([
            'data' => $data,
            'tipus' => 'no lectiu',
            'esdeveniment' => null,
        ]);
        foreach (['2026-09-21', '2026-09-23', '2026-09-25'] as $gaudi) {
            $this->crearPeticio('PROF01', $gaudi, AssumpteParticular::ESTAT_AUTORITZADA);
        }
        $this->crearPeticio(
            'PROF06',
            $data,
            AssumpteParticular::ESTAT_AUTORITZADA,
            null,
            AssumpteParticular::TORN_VESPRADA
        );
        $this->crearPeticio(
            'PROF07',
            $data,
            AssumpteParticular::ESTAT_AUTORITZADA,
            null,
            AssumpteParticular::TORN_VESPRADA
        );
        $this->crearPeticio(
            'PROF01',
            $data,
            AssumpteParticular::ESTAT_PENDENT,
            '2026-09-01 08:00:00',
            AssumpteParticular::TORN_VESPRADA
        );

        $avisos = app(AssumpteParticularDireccionQueryService::class)
            ->grupsPendents()[0]['peticions'][0]['avisos'];

        $this->assertTrue(collect($avisos)->contains(
            fn (string $avis): bool => str_contains($avis, 'tipus del dia ha canviat')
        ));
        $this->assertTrue(collect($avisos)->contains(
            fn (string $avis): bool => str_contains($avis, 'torn docent actual')
        ));
        $this->assertTrue(collect($avisos)->contains(
            fn (string $avis): bool => str_contains($avis, 'dia complet')
        ));
        $this->assertTrue(collect($avisos)->contains(
            fn (string $avis): bool => str_contains($avis, 'places disponibles')
        ));
    }

    public function test_renderitza_grups_i_no_oferix_accions_de_resolucio(): void
    {
        $this->crearPeticio(
            'PROF01',
            '2026-10-15',
            AssumpteParticular::ESTAT_PENDENT,
            '2026-09-01 08:00:00',
            AssumpteParticular::TORN_MATI,
            'Necessitat familiar sobrevinguda'
        );

        Livewire::actingAs($this->professor('DIR001'), 'profesor')
            ->test(AssumpteParticularDireccionPanel::class)
            ->assertSee('15/10/2026')
            ->assertSee('Professor 01')
            ->assertSee('Necessitat familiar sobrevinguda')
            ->assertSee('Excepcional')
            ->assertDontSeeHtml('wire:click="autoritzar')
            ->assertDontSeeHtml('wire:click="denegar');
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
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->string('sustituye_a')->nullable();
            $table->unsignedBigInteger('rol')->default(config('roles.rol.profesor'));
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
        Schema::create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('turno')->nullable();
            $table->string('hora_ini')->nullable();
            $table->string('hora_fin')->nullable();
        });
        Schema::create('horarios', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor');
            $table->string('modulo')->nullable();
            $table->string('idGrupo')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('aula')->nullable();
            $table->string('dia_semana', 1);
            $table->unsignedInteger('sesion_orden');
            $table->unsignedInteger('plantilla')->nullable();
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
            $table->string('estat');
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

    private function crearPlantilla(): void
    {
        $professors = [
            ['DIR001', 'Direcció', config('roles.rol.direccion'), false],
            ['ADM001', 'Administració', config('roles.rol.administrador'), false],
            ['PROF01', 'Professor 01', config('roles.rol.profesor'), true],
            ['PROF02', 'Professor 02', config('roles.rol.profesor'), true],
            ['PROF03', 'Professor 03', config('roles.rol.profesor'), true],
            ['PROF04', 'Professor 04', config('roles.rol.profesor'), true],
            ['PROF05', 'Professor 05', config('roles.rol.profesor'), true],
            ['PROF06', 'Professor 06', config('roles.rol.profesor'), true],
            ['PROF07', 'Professor 07', config('roles.rol.profesor'), true],
            ['PROF08', 'Professor 08', config('roles.rol.profesor'), true],
        ];

        foreach ($professors as [$dni, $nom, $rol, $actiu]) {
            DB::table('profesores')->insert([
                'dni' => $dni,
                'nombre' => $nom,
                'apellido1' => '',
                'apellido2' => '',
                'fecha_ingreso' => '2026-09-01',
                'rol' => $rol,
                'activo' => $actiu,
            ]);
        }

        DB::table('horas')->insert([
            ['codigo' => 1, 'turno' => 'M', 'hora_ini' => '08:00', 'hora_fin' => '08:55'],
            ['codigo' => 2, 'turno' => 'M', 'hora_ini' => '08:55', 'hora_fin' => '09:50'],
            ['codigo' => 8, 'turno' => 'V', 'hora_ini' => '15:00', 'hora_fin' => '15:55'],
        ]);

        $this->crearHora('PROF01', 1);
        $this->crearHora('PROF02', 1);
        $this->crearHora('PROF02', 8);
        $this->crearHora('PROF03', 1);
        $this->crearHora('PROF04', 1);
        $this->crearHora('PROF05', 2);
        $this->crearHora('PROF06', 8);
        $this->crearHora('PROF07', 8);
    }

    private function crearHora(string $dni, int $sessio): void
    {
        DB::table('horarios')->insert([
            'idProfesor' => $dni,
            'modulo' => 'M01',
            'dia_semana' => 'J',
            'sesion_orden' => $sessio,
        ]);
    }

    private function crearPeticio(
        string $dni,
        string $data,
        string $estat,
        ?string $sollicitadaAt = null,
        string $torn = AssumpteParticular::TORN_MATI,
        ?string $motivacio = null
    ): AssumpteParticular {
        return AssumpteParticular::query()->create([
            'idProfesor' => $dni,
            'data_gaudi' => $data,
            'curs' => '2026-2027',
            'tipus' => AssumpteParticular::TIPUS_LECTIU,
            'torn' => $torn,
            'estat' => $estat,
            'motivacio_excepcional' => $motivacio,
            'pla_activitats' => 'Pla',
            'sollicitada_at' => $sollicitadaAt,
        ]);
    }
}
