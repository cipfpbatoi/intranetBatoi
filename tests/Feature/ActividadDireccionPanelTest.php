<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Livewire\ActividadDireccionPanel;
use Livewire\Livewire;
use Tests\TestCase;

class ActividadDireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('actividad_direccion_panel_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('actividad_profesor');
        Schema::connection('sqlite')->dropIfExists('tipo_actividad');
        Schema::connection('sqlite')->dropIfExists('departamentos');
        Schema::connection('sqlite')->dropIfExists('actividades');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_renderitza_llistat_i_textos_clau(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component
            ->assertDontSee('Pilot funcional')
            ->assertSee('Imprimir activitats autoritzades (1)')
            ->assertSee('Autoritzar activitats pendents (1)')
            ->assertSee('Jordi Marti Perez')
            ->assertSee('Visita museu');

        $actividades = $component->get('actividades');

        $this->assertCount(4, $actividades);
        $this->assertTrue(collect($actividades)->contains(fn (array $actividad): bool => (bool) $actividad['hasDocument']));
    }

    public function test_filtra_per_professor_nom_d_activitat_estat_i_departament(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->set('filterProfessor', 'Jordi');

        $actividades = $component->get('actividades');
        $this->assertCount(2, $actividades);

        $component->set('filterProfessor', '');
        $component->set('filterEstat', '3');

        $actividades = $component->get('actividades');
        $this->assertCount(1, $actividades);
        $this->assertSame(3, $actividades[0]['estado']);

        $component->set('filterEstat', '');
        $component->set('filterProfessor', 'museu');

        $actividades = $component->get('actividades');
        $this->assertCount(1, $actividades);
        $this->assertSame('Visita museu', $actividades[0]['name']);

        $component->set('filterProfessor', '');
        $component->set('filterDepartament', '11');

        $actividades = $component->get('actividades');
        $this->assertCount(1, $actividades);
        $this->assertSame(3, $actividades[0]['id']);
    }

    public function test_acceptar_i_desautoritzar_actualitzen_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->call('acceptar', 1)
            ->assertSet('error', '')
            ->assertSet('message', 'Activitat autoritzada correctament.');

        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 1)->value('estado'));

        $component->call('desautoritzar', 3)
            ->assertSet('error', '')
            ->assertSet('message', 'Activitat retornada a l\'estat anterior.');

        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 3)->value('estado'));
    }

    public function test_marcar_itaca_actualitza_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->call('marcarItaca', 4)
            ->assertSet('error', '')
            ->assertSet('message', 'Activitat marcada com a tramitada en ITACA.');

        $this->assertSame(5, (int) DB::connection('sqlite')->table('actividades')->where('id', 4)->value('estado'));
    }

    public function test_mostrar_carrega_activitat_seleccionada(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->call('mostrar', 2)
            ->assertSet('selectedActividad.id', 2)
            ->assertSet('selectedActividad.name', 'Jornada convivencia')
            ->assertSet('selectedActividad.coordinador', 'Jordi Marti Perez');
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

        Schema::connection('sqlite')->create('actividades', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->unsignedInteger('tipo_actividad_id')->nullable();
            $table->boolean('extraescolar')->default(true);
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->boolean('complementaria')->default(true);
            $table->boolean('fueraCentro')->default(true);
            $table->boolean('transport')->default(false);
            $table->text('objetivos')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('comentarios')->nullable();
            $table->boolean('poll')->default(false);
            $table->text('desenvolupament')->nullable();
            $table->text('valoracio')->nullable();
            $table->text('aspectes')->nullable();
            $table->text('dades')->nullable();
            $table->unsignedBigInteger('idDocumento')->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('departamentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
            $table->unsignedInteger('idProfesor')->nullable();
            $table->string('depcurt')->nullable();
            $table->boolean('didactico')->default(true);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('tipo_actividad', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
            $table->unsignedInteger('departamento_id')->nullable();
            $table->text('justificacio')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('actividad_profesor', function (Blueprint $table): void {
            $table->unsignedInteger('idActividad');
            $table->string('idProfesor', 10);
            $table->boolean('coordinador')->default(false);
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
                'dni' => 'ACT100',
                'nombre' => 'Jordi',
                'apellido1' => 'Marti',
                'apellido2' => 'Perez',
                'email' => 'act100@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'ACT200',
                'nombre' => 'Anna',
                'apellido1' => 'Soler',
                'apellido2' => 'Lopez',
                'email' => 'act200@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('departamentos')->insert([
            [
                'id' => 10,
                'cliteral' => 'Dep. Arts',
                'vliteral' => 'Departament d\'Arts',
                'idProfesor' => null,
                'depcurt' => 'ART',
                'didactico' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'cliteral' => 'Dep. Història',
                'vliteral' => 'Departament d\'Història',
                'idProfesor' => null,
                'depcurt' => 'HIS',
                'didactico' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('tipo_actividad')->insert([
            [
                'id' => 7,
                'cliteral' => 'Eixida',
                'vliteral' => 'Eixida cultural',
                'departamento_id' => 10,
                'justificacio' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'cliteral' => 'Teatre',
                'vliteral' => 'Representació teatral',
                'departamento_id' => 11,
                'justificacio' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('actividades')->insert([
            [
                'id' => 1,
                'name' => 'Visita museu',
                'tipo_actividad_id' => 7,
                'descripcion' => 'Eixida cultural',
                'extraescolar' => 1,
                'desde' => '2026-03-20 09:00:00',
                'hasta' => '2026-03-20 13:00:00',
                'complementaria' => 1,
                'fueraCentro' => 1,
                'transport' => 0,
                'idDocumento' => null,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Jornada convivencia',
                'tipo_actividad_id' => 7,
                'descripcion' => 'Activitat autoritzada',
                'extraescolar' => 1,
                'desde' => '2026-03-21 08:00:00',
                'hasta' => '2026-03-21 14:00:00',
                'complementaria' => 1,
                'fueraCentro' => 1,
                'transport' => 0,
                'idDocumento' => 99,
                'estado' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Teatre escolar',
                'tipo_actividad_id' => 8,
                'descripcion' => 'Registrada',
                'extraescolar' => 1,
                'desde' => '2026-03-10 10:00:00',
                'hasta' => '2026-03-10 12:00:00',
                'complementaria' => 1,
                'fueraCentro' => 1,
                'transport' => 0,
                'idDocumento' => null,
                'estado' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Fira formativa',
                'tipo_actividad_id' => 7,
                'descripcion' => 'Pendent ITACA',
                'extraescolar' => 1,
                'desde' => '2026-03-05 09:30:00',
                'hasta' => '2026-03-05 12:30:00',
                'complementaria' => 0,
                'fueraCentro' => 1,
                'transport' => 1,
                'idDocumento' => null,
                'estado' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('actividad_profesor')->insert([
            ['idActividad' => 1, 'idProfesor' => 'ACT100', 'coordinador' => 1],
            ['idActividad' => 2, 'idProfesor' => 'ACT100', 'coordinador' => 1],
            ['idActividad' => 3, 'idProfesor' => 'ACT200', 'coordinador' => 1],
            ['idActividad' => 4, 'idProfesor' => 'ACT200', 'coordinador' => 1],
        ]);
    }
}
