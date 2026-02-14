<?php

declare(strict_types=1);

namespace Tests\Feature;

use Intranet\Livewire\HorariProfessorCanvi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HorariProfessorCanviTest extends TestCase
{
    protected string $dni = '123A';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Storage::fake('local');

        $this->createSchema();
        $this->seedBaseData();
    }

    public function test_mou_hores_i_fa_intercanvi(): void
    {
        $this->createHorario($this->dni, 1, 'L', 'M01', 'G1');
        $this->createHorario($this->dni, 2, 'L', 'M01', 'G1');

        $component = Livewire::test(HorariProfessorCanvi::class, ['dni' => $this->dni]);

        $grid = $component->get('grid');
        $id1 = $grid['1-L'];
        $id2 = $grid['2-L'];

        $component->call('moveFromTo', '1-L', '2-L')
            ->assertSet('error', '')
            ->assertSet('message', 'Canvi aplicat.');

        $items = $component->get('items');
        $this->assertSame('2-L', $items[$id1]['cell']);
        $this->assertSame('1-L', $items[$id2]['cell']);
    }

    public function test_no_permet_canviar_de_dia(): void
    {
        $this->createHorario($this->dni, 1, 'L', 'M01', 'G1');
        $this->createHorario($this->dni, 1, 'M', 'M01', 'G1');

        $component = Livewire::test(HorariProfessorCanvi::class, ['dni' => $this->dni]);

        $component->call('moveFromTo', '1-L', '1-M')
            ->assertSet('error', "No pots canviar una hora d'un dia a un altre.");

        $grid = $component->get('grid');
        $items = $component->get('items');
        $id1 = $grid['1-L'];
        $this->assertSame('1-L', $items[$id1]['cell']);
    }

    public function test_no_permet_canviar_de_torn(): void
    {
        $this->createHorario($this->dni, 1, 'L', 'M01', 'G1');

        $component = Livewire::test(HorariProfessorCanvi::class, ['dni' => $this->dni]);

        $component->call('moveFromTo', '1-L', '6-L')
            ->assertSet('error', 'No pots moure una hora del mati a la vesprada (ni al reves).');

        $grid = $component->get('grid');
        $items = $component->get('items');
        $id1 = $grid['1-L'];
        $this->assertSame('1-L', $items[$id1]['cell']);
    }

    public function test_no_permet_moure_guardies(): void
    {
        $guardia = config('constants.ocupacionesGuardia.normal');

        $this->createHorario($this->dni, 1, 'L', 'M01', 'G1');
        $this->createHorario($this->dni, 2, 'L', null, null, $guardia);

        $component = Livewire::test(HorariProfessorCanvi::class, ['dni' => $this->dni]);

        $component->call('moveFromTo', '2-L', '1-L')
            ->assertSet('error', 'No es poden canviar les guardies.');

        $grid = $component->get('grid');
        $items = $component->get('items');
        $idGuardia = $grid['2-L'];
        $this->assertSame('2-L', $items[$idGuardia]['cell']);
    }

    public function test_guarda_proposta_i_crea_fitxer(): void
    {
        $this->createHorario($this->dni, 1, 'L', 'M01', 'G1');
        $this->createHorario($this->dni, 2, 'L', 'M01', 'G1');

        $component = Livewire::test(HorariProfessorCanvi::class, ['dni' => $this->dni]);

        $component->call('moveFromTo', '1-L', '2-L');

        $component
            ->set('obs', 'Canvi temporal per necessitat docent.')
            ->set('fechaInicio', '2026-02-10')
            ->set('fechaFin', '2026-02-12')
            ->set('declaraciones', [
                'mantenimiento_turno' => true,
                'ausencia_alumnado' => true,
                'servicios_inamovibles' => true,
                'atencion_refuerzo' => true,
                'permanencia' => true,
            ])
            ->call('guardarProposta')
            ->assertSet('error', '')
            ->assertSet('estado', 'Pendiente')
            ->assertSet('message', 'Sol·licitud enviada a direccio.');

        $dir = '/horarios/' . $this->dni;
        $files = Storage::disk('local')->allFiles($dir);

        $this->assertCount(1, $files);

        $data = json_decode(Storage::disk('local')->get($files[0]), true);
        $this->assertSame('Pendiente', $data['estado']);
        $this->assertSame($this->dni, $data['dni']);
        $this->assertSame('2026-02-10', $data['fecha_inicio']);
        $this->assertSame('2026-02-12', $data['fecha_fin']);
        $this->assertCount(2, $data['cambios']);
    }

    protected function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->dropIfExists('horarios');
        $schema->dropIfExists('ocupaciones');
        $schema->dropIfExists('grupos');
        $schema->dropIfExists('modulos');
        $schema->dropIfExists('horas');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre');
            $table->string('apellido1');
            $table->string('apellido2');
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a')->nullable();
            $table->timestamps();
        });

        $schema->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('turno');
            $table->string('hora_ini');
            $table->string('hora_fin');
        });

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('cliteral');
            $table->string('vliteral')->nullable();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre');
            $table->string('turno')->nullable();
            $table->timestamps();
        });

        $schema->create('ocupaciones', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('nom')->nullable();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->string('modulo')->nullable();
            $table->string('idGrupo')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('aula')->nullable();
            $table->string('dia_semana');
            $table->unsignedInteger('sesion_orden');
            $table->string('plantilla')->nullable();
            $table->timestamps();
        });
    }

    protected function seedBaseData(): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => $this->dni,
            'nombre' => 'Anna',
            'apellido1' => 'Boto',
            'apellido2' => 'Ibor',
            'rol' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('sqlite')->table('horas')->insert([
            [
                'codigo' => 1,
                'turno' => 'M',
                'hora_ini' => '08:00',
                'hora_fin' => '09:00',
            ],
            [
                'codigo' => 2,
                'turno' => 'M',
                'hora_ini' => '09:00',
                'hora_fin' => '10:00',
            ],
            [
                'codigo' => 6,
                'turno' => 'V',
                'hora_ini' => '15:00',
                'hora_fin' => '16:00',
            ],
        ]);

        DB::connection('sqlite')->table('modulos')->insert([
            'codigo' => 'M01',
            'cliteral' => 'Modul 1',
            'vliteral' => 'Modul 1',
        ]);

        DB::connection('sqlite')->table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => '1A',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('sqlite')->table('ocupaciones')->insert([
            'codigo' => config('constants.ocupacionesGuardia.normal'),
            'nombre' => 'Guardia',
            'nom' => 'Guardia',
        ]);
    }

    protected function createHorario(
        string $dni,
        int $sesion,
        string $dia,
        ?string $modulo,
        ?string $grupo,
        ?string $ocupacion = null
    ): void {
        DB::connection('sqlite')->table('horarios')->insert([
            'idProfesor' => $dni,
            'modulo' => $modulo,
            'idGrupo' => $grupo,
            'ocupacion' => $ocupacion,
            'aula' => 'A1',
            'dia_semana' => $dia,
            'sesion_orden' => $sesion,
            'plantilla' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
