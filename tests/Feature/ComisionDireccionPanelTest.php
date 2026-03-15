<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Livewire\ComisionDireccionPanel;
use Livewire\Livewire;
use Tests\TestCase;

class ComisionDireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('comision_direccion_panel_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->seedBaseData();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_renderitza_llistat_i_textos_clau(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ComisionDireccionPanel::class);

        $component
            ->assertSee('Pilot funcional')
            ->assertSee('Imprimir Comissions autoritzades')
            ->assertSee('Maria Garcia Lopez')
            ->assertSee('Joan Soler Perez');

        $comisiones = $component->get('comisiones');
        $pendingPayments = $component->get('pendingPayments');

        $this->assertCount(4, $comisiones);
        $this->assertCount(1, $pendingPayments);
        $this->assertSame('CM200', $pendingPayments[0]['dni']);
        $this->assertTrue((bool) $comisiones[0]['hasDocument']);
        $this->assertTrue((bool) $comisiones[1]['hasDocument']);
    }

    public function test_filtra_per_professor_i_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ComisionDireccionPanel::class);

        $component->set('filterProfessor', 'Maria');

        $comisiones = $component->get('comisiones');
        $this->assertCount(2, $comisiones);
        $this->assertSame(['CM100', 'CM100'], array_column($comisiones, 'idProfesor'));

        $component->set('filterEstat', '2');

        $comisiones = $component->get('comisiones');
        $this->assertCount(1, $comisiones);
        $this->assertSame(2, $comisiones[0]['estado']);
    }

    public function test_acceptar_i_desautoritzar_actualitzen_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ComisionDireccionPanel::class);

        $component->call('acceptar', 1)
            ->assertSet('error', '')
            ->assertSet('message', 'Comissió autoritzada correctament.');

        $this->assertSame(2, (int) DB::connection('sqlite')->table('comisiones')->where('id', 1)->value('estado'));

        $component->call('desautoritzar', 2)
            ->assertSet('error', '')
            ->assertSet('message', 'Comissió retornada a pendent.');

        $this->assertSame(1, (int) DB::connection('sqlite')->table('comisiones')->where('id', 2)->value('estado'));

        $component->call('esborrar', 3)
            ->assertSet('error', '')
            ->assertSet('message', 'Comissió esborrada correctament.');

        $this->assertNull(DB::connection('sqlite')->table('comisiones')->where('id', 3)->first());
    }

    public function test_no_es_pot_esborrar_una_comissio_cobrada(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ComisionDireccionPanel::class);

        $component->call('esborrar', 4)
            ->assertSet('error', 'No es poden esborrar comissions cobrades.');

        $this->assertNotNull(DB::connection('sqlite')->table('comisiones')->where('id', 4)->first());
    }

    public function test_mostrar_carrega_la_comissio_seleccionada(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ComisionDireccionPanel::class);

        $component->call('mostrar', 3)
            ->assertSet('selectedComision.id', 3)
            ->assertSet('selectedComision.professor', 'Joan Soler Perez')
            ->assertSet('selectedComision.servicio', 'Visita empresa 3');
    }

    public function test_imprimir_pagaments_seleccionats_marca_professor_i_redirigeix(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ComisionDireccionPanel::class);

        $component->set('selectedPayments', ['CM200'])
            ->call('imprimirPagamentsSeleccionats')
            ->assertDispatched('open-payments-report', url: route('comision.paid'));

        $this->assertSame(6, (int) DB::connection('sqlite')->table('comisiones')->where('id', 3)->value('estado'));
        $this->assertSame(5, (int) DB::connection('sqlite')->table('comisiones')->where('id', 4)->value('estado'));
    }

    private function direccionUser(): Profesor
    {
        return Profesor::on('sqlite')->findOrFail('DIR001');
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
            $table->boolean('activo')->default(true);
            $table->date('fecha_baja')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor', 10)->nullable();
            $table->unsignedBigInteger('idDocumento')->nullable();
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->unsignedTinyInteger('fct')->default(0);
            $table->text('servicio')->nullable();
            $table->decimal('alojamiento', 8, 2)->default(0);
            $table->decimal('comida', 8, 2)->default(0);
            $table->decimal('gastos', 8, 2)->default(0);
            $table->unsignedInteger('kilometraje')->default(0);
            $table->unsignedTinyInteger('medio')->default(0);
            $table->string('marca')->nullable();
            $table->string('matricula')->nullable();
            $table->text('itinerario')->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('action')->nullable();
            $table->text('comentari')->nullable();
            $table->string('document')->nullable();
            $table->string('model_class')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('author_id')->nullable();
            $table->timestamps();
        });
    }

    private function seedBaseData(): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            [
                'dni' => 'DIR001',
                'nombre' => 'Direccio',
                'apellido1' => 'Centre',
                'apellido2' => 'Test',
                'email' => 'dir@test.local',
                'rol' => config('roles.rol.direccion'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'CM100',
                'nombre' => 'Maria',
                'apellido1' => 'Garcia',
                'apellido2' => 'Lopez',
                'email' => 'cm100@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'CM200',
                'nombre' => 'Joan',
                'apellido1' => 'Soler',
                'apellido2' => 'Perez',
                'email' => 'cm200@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('comisiones')->insert([
            [
                'id' => 1,
                'idProfesor' => 'CM100',
                'idDocumento' => 3001,
                'desde' => '2026-03-10 09:00:00',
                'hasta' => '2026-03-10 12:00:00',
                'servicio' => 'Visita empresa 1',
                'kilometraje' => 10,
                'gastos' => 5,
                'comida' => 0,
                'alojamiento' => 0,
                'medio' => 0,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'idProfesor' => 'CM100',
                'idDocumento' => 3002,
                'desde' => '2026-03-11 09:00:00',
                'hasta' => '2026-03-11 12:00:00',
                'servicio' => 'Visita empresa 2',
                'kilometraje' => 20,
                'gastos' => 0,
                'comida' => 10,
                'alojamiento' => 0,
                'medio' => 0,
                'estado' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'idProfesor' => 'CM200',
                'desde' => '2026-03-12 09:00:00',
                'hasta' => '2026-03-12 12:00:00',
                'servicio' => 'Visita empresa 3',
                'kilometraje' => 0,
                'gastos' => 25,
                'comida' => 0,
                'alojamiento' => 0,
                'medio' => 2,
                'estado' => 4,
                'idDocumento' => 3003,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'idProfesor' => 'CM200',
                'desde' => '2026-03-13 09:00:00',
                'hasta' => '2026-03-13 12:00:00',
                'servicio' => 'Visita empresa 4',
                'kilometraje' => 15,
                'gastos' => 0,
                'comida' => 0,
                'alojamiento' => 0,
                'medio' => 0,
                'estado' => 5,
                'idDocumento' => 3004,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
