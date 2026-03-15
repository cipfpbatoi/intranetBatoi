<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Livewire\ExpedienteDireccionPanel;
use Livewire\Livewire;
use Tests\TestCase;

class ExpedienteDireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('expediente_direccion_panel_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('tipo_expedientes');
        Schema::connection('sqlite')->dropIfExists('modulos');
        Schema::connection('sqlite')->dropIfExists('alumnos');
        Schema::connection('sqlite')->dropIfExists('expedientes');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_renderitza_llistat_i_textos_clau(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ExpedienteDireccionPanel::class);

        $component
            ->assertSee('Pilot funcional')
            ->assertSee('Imprimir expedients autoritzats (1)')
            ->assertSee('Autoritzar expedients pendents (1)')
            ->assertSee('Maria Garcia Lopez')
            ->assertSee('Conducta');

        $expedientes = $component->get('expedientes');

        $this->assertCount(4, $expedientes);
        $this->assertTrue(collect($expedientes)->contains(fn (array $expediente): bool => (bool) $expediente['hasDocument']));
    }

    public function test_filtra_per_text_i_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ExpedienteDireccionPanel::class);

        $component->set('filterText', 'Informe');

        $expedientes = $component->get('expedientes');
        $this->assertCount(1, $expedientes);
        $this->assertSame('Informe', $expedientes[0]['tipo']);

        $component->set('filterText', '');
        $component->set('filterEstat', '2');

        $expedientes = $component->get('expedientes');
        $this->assertCount(1, $expedientes);
        $this->assertSame(2, $expedientes[0]['estado']);
    }

    public function test_acceptar_i_desautoritzar_actualitzen_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ExpedienteDireccionPanel::class);

        $component->call('acceptar', 1)
            ->assertSet('error', '')
            ->assertSet('message', 'Expedient autoritzat correctament.');

        $this->assertSame(2, (int) DB::connection('sqlite')->table('expedientes')->where('id', 1)->value('estado'));

        $component->call('desautoritzar', 2)
            ->assertSet('error', '')
            ->assertSet('message', 'Expedient retornat a pendent.');

        $this->assertSame(1, (int) DB::connection('sqlite')->table('expedientes')->where('id', 2)->value('estado'));
    }

    public function test_mostrar_carrega_l_expedient_seleccionat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ExpedienteDireccionPanel::class);

        $component->call('mostrar', 3)
            ->assertSet('selectedExpediente.id', 3)
            ->assertSet('selectedExpediente.nomAlum', 'Joan Soler Perez')
            ->assertSet('selectedExpediente.tipo', 'Conducta');
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

        Schema::connection('sqlite')->create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
        });

        Schema::connection('sqlite')->create('modulos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        Schema::connection('sqlite')->create('tipo_expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('titulo')->nullable();
            $table->string('vista')->nullable();
            $table->boolean('informe')->default(false);
            $table->boolean('orientacion')->default(false);
            $table->unsignedInteger('rol')->nullable();
        });

        Schema::connection('sqlite')->create('expedientes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('tipo')->nullable();
            $table->string('idModulo')->nullable();
            $table->string('idAlumno')->nullable();
            $table->string('idProfesor')->nullable();
            $table->text('explicacion')->nullable();
            $table->date('fecha')->nullable();
            $table->date('fechatramite')->nullable();
            $table->unsignedBigInteger('idDocumento')->nullable();
            $table->tinyInteger('estado')->default(0);
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
                'dni' => 'PR100',
                'nombre' => 'Maria',
                'apellido1' => 'Garcia',
                'apellido2' => 'Lopez',
                'email' => 'pr100@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'PR200',
                'nombre' => 'Joan',
                'apellido1' => 'Soler',
                'apellido2' => 'Perez',
                'email' => 'pr200@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('alumnos')->insert([
            ['nia' => 'AL001', 'nombre' => 'Maria', 'apellido1' => 'Garcia', 'apellido2' => 'Lopez', 'email' => 'al001@test.local'],
            ['nia' => 'AL002', 'nombre' => 'Joan', 'apellido1' => 'Soler', 'apellido2' => 'Perez', 'email' => 'al002@test.local'],
        ]);

        DB::connection('sqlite')->table('modulos')->insert([
            ['codigo' => 'M01', 'cliteral' => 'Tutoria', 'vliteral' => 'Tutoria'],
            ['codigo' => 'M02', 'cliteral' => 'Convivencia', 'vliteral' => 'Convivencia'],
        ]);

        DB::connection('sqlite')->table('tipo_expedientes')->insert([
            ['id' => 1, 'titulo' => 'Conducta', 'vista' => 'conducta', 'informe' => 0, 'orientacion' => 0, 'rol' => config('roles.rol.direccion')],
            ['id' => 2, 'titulo' => 'Informe', 'vista' => 'informe', 'informe' => 1, 'orientacion' => 0, 'rol' => config('roles.rol.direccion')],
        ]);

        DB::connection('sqlite')->table('expedientes')->insert([
            [
                'id' => 1,
                'tipo' => 1,
                'idModulo' => 'M01',
                'idAlumno' => 'AL001',
                'idProfesor' => 'PR100',
                'explicacion' => 'Expedient pendent',
                'fecha' => '2026-03-10',
                'fechatramite' => null,
                'idDocumento' => null,
                'estado' => 1,
            ],
            [
                'id' => 2,
                'tipo' => 2,
                'idModulo' => 'M02',
                'idAlumno' => 'AL001',
                'idProfesor' => 'PR100',
                'explicacion' => 'Expedient autoritzat',
                'fecha' => '2026-03-11',
                'fechatramite' => '2026-03-12',
                'idDocumento' => 88,
                'estado' => 2,
            ],
            [
                'id' => 3,
                'tipo' => 1,
                'idModulo' => 'M02',
                'idAlumno' => 'AL002',
                'idProfesor' => 'PR200',
                'explicacion' => 'Expedient resolt',
                'fecha' => '2026-03-09',
                'fechatramite' => '2026-03-13',
                'idDocumento' => null,
                'estado' => 3,
            ],
            [
                'id' => 4,
                'tipo' => 1,
                'idModulo' => 'M01',
                'idAlumno' => 'AL002',
                'idProfesor' => 'PR200',
                'explicacion' => 'Expedient imprés',
                'fecha' => '2026-03-08',
                'fechatramite' => '2026-03-14',
                'idDocumento' => 99,
                'estado' => 4,
            ],
        ]);
    }
}
