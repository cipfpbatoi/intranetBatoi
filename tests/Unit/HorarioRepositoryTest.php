<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Horario\HorarioRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Horario\EloquentHorarioRepository;
use Tests\TestCase;

class HorarioRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('horario_repository_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        config(['constants.ocupacionesGuardia' => [90, 91]]);
        config(['constants.ocupacionesGuardia.normal' => 90]);
        config(['constants.ocupacionesGuardia.biblio' => 91]);
        config(['constants.modulosNoLectivos' => ['NOLECT']]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->seedData();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('horarios');
        Schema::connection('sqlite')->dropIfExists('horas');
        Schema::connection('sqlite')->dropIfExists('modulos');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('ocupaciones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(HorarioRepositoryInterface::class);
        $this->assertInstanceOf(EloquentHorarioRepository::class, $repo);
    }

    public function test_semanal_i_comptadors_basics(): void
    {
        $repo = $this->app->make(HorarioRepositoryInterface::class);

        $setmanal = $repo->semanalByProfesor('P001');
        $this->assertIsArray($setmanal);
        $this->assertArrayHasKey('L', $setmanal);

        $grup = $repo->semanalByGrupo('G1');
        $this->assertIsArray($grup);
        $this->assertArrayHasKey('L', $grup);

        $this->assertSame(2, $repo->countByProfesorAndDay('P001', 'L'));

        $lectivas = $repo->lectivasByProfesorAndDayOrdered('P001', 'L');
        $this->assertCount(1, $lectivas);
        $this->assertSame(1, (int) $lectivas->first()->sesion_orden);
    }

    public function test_queries_guardia_i_lectives(): void
    {
        $repo = $this->app->make(HorarioRepositoryInterface::class);

        $lectives = $repo->lectivosByDayAndSesion('L', 1);
        $this->assertCount(1, $lectives);
        $this->assertSame('M1', (string) $lectives->first()->modulo);

        $guardiaDia = $repo->guardiaAllByDia('L');
        $this->assertCount(1, $guardiaDia);

        $guardiaByProfe = $repo->guardiaAllByProfesorAndDia('P001', 'L');
        $this->assertCount(1, $guardiaByProfe);

        $guardiaByProfeAndSesions = $repo->guardiaAllByProfesorAndDiaAndSesiones('P001', 'L', [2]);
        $this->assertCount(1, $guardiaByProfeAndSesions);

        $guardiaByProfe = $repo->guardiaAllByProfesor('P001');
        $this->assertCount(1, $guardiaByProfe);

        $updated = $repo->reassignProfesor('P002', 'P001');
        $this->assertSame(1, $updated);
        $this->assertSame(0, DB::table('horarios')->where('idProfesor', 'P002')->count());

        $deleted = $repo->deleteByProfesor('P001');
        $this->assertGreaterThanOrEqual(1, $deleted);
    }

    public function test_first_i_primera_by_profesor_date(): void
    {
        $repo = $this->app->make(HorarioRepositoryInterface::class);

        $first = $repo->firstByProfesorDiaSesion('P001', 'L', 1);
        $this->assertNotNull($first);
        $this->assertSame('M1', (string) $first->modulo);

        $primera = $repo->primeraByProfesorAndDateOrdered('P001', '2026-02-16');
        $this->assertCount(2, $primera);
        $this->assertSame(1, (int) $primera->first()->sesion_orden);

        $byModulo = $repo->firstByModulo('M1');
        $this->assertNotNull($byModulo);
        $this->assertSame('P001', (string) $byModulo->idProfesor);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('sustituye_a', 10)->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->date('fecha_baja')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('hora_ini')->nullable();
            $table->string('hora_fin')->nullable();
        });

        Schema::connection('sqlite')->create('modulos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
        });

        Schema::connection('sqlite')->create('ocupaciones', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('nom')->nullable();
        });

        Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('idGrupo')->nullable();
            $table->string('dia_semana');
            $table->unsignedInteger('sesion_orden');
            $table->unsignedInteger('ocupacion')->nullable();
            $table->string('modulo')->nullable();
            $table->string('aula')->nullable();
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P001', 'sustituye_a' => null, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P002', 'sustituye_a' => null, 'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('horas')->insert([
            ['codigo' => 1, 'hora_ini' => '08:00', 'hora_fin' => '09:00'],
            ['codigo' => 2, 'hora_ini' => '09:00', 'hora_fin' => '10:00'],
        ]);

        DB::table('modulos')->insert([
            ['codigo' => 'M1', 'cliteral' => 'Modulo 1', 'vliteral' => 'Modulo 1'],
            ['codigo' => 'NOLECT', 'cliteral' => 'No Lectiu', 'vliteral' => 'No Lectiu'],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup 1'],
        ]);

        DB::table('ocupaciones')->insert([
            ['codigo' => 90, 'nombre' => 'Guardia', 'nom' => 'Guardia'],
            ['codigo' => 91, 'nombre' => 'Biblioteca', 'nom' => 'Biblioteca'],
        ]);

        DB::table('horarios')->insert([
            [
                'idProfesor' => 'P001',
                'idGrupo' => 'G1',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'M1',
                'aula' => 'A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'P001',
                'idGrupo' => null,
                'dia_semana' => 'L',
                'sesion_orden' => 2,
                'ocupacion' => 90,
                'modulo' => 'NOLECT',
                'aula' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'P002',
                'idGrupo' => 'G1',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'ocupacion' => null,
                'modulo' => 'NOLECT',
                'aula' => 'A2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
