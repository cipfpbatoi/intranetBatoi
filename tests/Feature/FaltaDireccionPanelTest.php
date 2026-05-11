<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Livewire\FaltaDireccionPanel;
use Livewire\Livewire;
use Tests\TestCase;

class FaltaDireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('falta_direccion_panel_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('faltas');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_renderitza_llistat_inicial_i_textos_clau(): void
    {
        $component = $this->actingAsDireccion()
            ? Livewire::test(FaltaDireccionPanel::class)
            : null;

        $component
            ->assertSee('Nova')
            ->assertSee('Maria Garcia Lopez')
            ->assertSee('Joan Soler Perez');

        $faltes = $component->get('faltes');

        $this->assertCount(3, $faltes);
    }

    public function test_filtra_per_professor_mentre_escriu_nom_o_dni(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $component->set('filterProfessor', 'Maria');

        $faltes = $component->get('faltes');

        $this->assertCount(2, $faltes);
        $this->assertSame(['PF100', 'PF100'], array_column($faltes, 'idProfesor'));

        $component->set('filterProfessor', 'PF200');

        $faltes = $component->get('faltes');

        $this->assertCount(1, $faltes);
        $this->assertSame('PF200', $faltes[0]['idProfesor']);
    }

    public function test_filtra_per_estat_textual(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $component
            ->set('filterEstat', '2')
            ->assertSee('Justificada')
            ->assertDontSee('Sense justificant')
            ->assertDontSee('Autoritzada');

        $faltes = $component->get('faltes');

        $this->assertCount(1, $faltes);
        $this->assertSame(2, $faltes[0]['estado']);
    }

    public function test_esborrar_permet_estat_1_o_2_i_rebutja_autoritzades(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $component->call('esborrar', 1)
            ->assertSet('error', '')
            ->assertSet('message', 'Falta esborrada correctament.');

        $this->assertNull(DB::connection('sqlite')->table('faltas')->where('id', 1)->first());

        $component->call('esborrar', 3)
            ->assertSet('error', 'Només es poden esborrar faltes sense autoritzar.');

        $this->assertNotNull(DB::connection('sqlite')->table('faltas')->where('id', 3)->first());
    }

    public function test_crea_una_falta_des_del_formulari_livewire(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('crear')
            ->set('formProfessorSearch', 'Maria Garcia Lopez (PF100)')
            ->set('formDesde', '2026-03-15')
            ->set('formHasta', '2026-03-15')
            ->set('formDiaCompleto', true)
            ->set('formMotivos', '2')
            ->set('formObservaciones', 'Nova falta des de Livewire')
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta creada correctament.');

        $this->assertSame(4, DB::connection('sqlite')->table('faltas')->count());

        $falta = DB::connection('sqlite')->table('faltas')->where('observaciones', 'Nova falta des de Livewire')->first();
        $this->assertNotNull($falta);
        $this->assertSame('PF100', $falta->idProfesor);
    }

    public function test_edita_una_falta_sense_endpoint_edit_data(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('editar', 2)
            ->assertSet('formFaltaId', 2)
            ->set('formObservaciones', 'Editada des de Livewire')
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta actualitzada correctament.');

        $this->assertSame(
            'Editada des de Livewire',
            DB::connection('sqlite')->table('faltas')->where('id', 2)->value('observaciones')
        );
    }

    public function test_edita_una_falta_autoritzada(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('editar', 3)
            ->assertSet('formFaltaId', 3)
            ->set('formObservaciones', 'Autoritzada editada des de direccio')
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta actualitzada correctament.');

        $this->assertSame(
            'Autoritzada editada des de direccio',
            DB::connection('sqlite')->table('faltas')->where('id', 3)->value('observaciones')
        );
    }

    private function actingAsDireccion(): bool
    {
        $this->actingAs($this->direccionUser(), 'profesor');

        return true;
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
            $table->string('api_token', 80)->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
            $table->unsignedInteger('departamento')->nullable();
            $table->boolean('activo')->default(true);
            $table->date('fecha_baja')->nullable();
            $table->string('sustituye_a', 10)->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('faltas', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor', 10);
            $table->boolean('baja')->default(false);
            $table->boolean('dia_completo')->default(true);
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->time('hora_ini')->nullable();
            $table->time('hora_fin')->nullable();
            $table->unsignedInteger('motivos')->nullable();
            $table->string('observaciones', 200)->nullable();
            $table->string('fichero')->nullable();
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

        Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
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
                'dni' => 'PF100',
                'nombre' => 'Maria',
                'apellido1' => 'Garcia',
                'apellido2' => 'Lopez',
                'email' => 'pf100@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'PF200',
                'nombre' => 'Joan',
                'apellido1' => 'Soler',
                'apellido2' => 'Perez',
                'email' => 'pf200@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('faltas')->insert([
            [
                'id' => 1,
                'idProfesor' => 'PF100',
                'baja' => 0,
                'dia_completo' => 1,
                'desde' => '2026-03-10',
                'hasta' => '2026-03-10',
                'motivos' => 1,
                'observaciones' => 'Sense document',
                'fichero' => null,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'idProfesor' => 'PF100',
                'baja' => 0,
                'dia_completo' => 1,
                'desde' => '2026-03-11',
                'hasta' => '2026-03-11',
                'motivos' => 2,
                'observaciones' => 'Amb document',
                'fichero' => 'faltas/doc.pdf',
                'estado' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'idProfesor' => 'PF200',
                'baja' => 0,
                'dia_completo' => 1,
                'desde' => '2026-03-12',
                'hasta' => '2026-03-12',
                'motivos' => 3,
                'observaciones' => 'Autoritzada',
                'fichero' => null,
                'estado' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
