<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Grupo\GrupoRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Grupo\EloquentGrupoRepository;
use Tests\TestCase;

class GrupoRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('grupo_repository_testing.sqlite');
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
        $this->seedData();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('alumnos_grupos');
        Schema::connection('sqlite')->dropIfExists('alumnos');
        Schema::connection('sqlite')->dropIfExists('ciclos');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);
        $this->assertInstanceOf(EloquentGrupoRepository::class, $repo);
    }

    public function test_find_i_all_i_by_curso(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $find = $repo->find('G1');
        $this->assertNotNull($find);
        $this->assertSame('G1', (string) $find->codigo);

        $all = $repo->all();
        $this->assertCount(3, $all);

        $curso2 = $repo->byCurso(2);
        $this->assertCount(2, $curso2);
    }

    public function test_create_persistix_grup(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $created = $repo->create([
            'codigo' => 'G9',
            'nombre' => 'Grup 9',
            'tutor' => 'P2',
            'idCiclo' => 2,
            'curso' => 1,
            'acta_pendiente' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame('G9', (string) $created->codigo);
        $this->assertSame('Grup 9', DB::table('grupos')->where('codigo', 'G9')->value('nombre'));
    }

    public function test_q_tutor_i_largest_by_tutor(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $qtutor = $repo->qTutor('P1')->pluck('codigo')->sort()->values()->all();
        $this->assertSame(['G1', 'G2'], $qtutor);

        $first = $repo->firstByTutor('P1');
        $this->assertNotNull($first);

        $largest = $repo->largestByTutor('P1');
        $this->assertNotNull($largest);
        $this->assertSame((string) $first->codigo, (string) $largest->codigo);
    }

    public function test_by_departamento_i_tutores_dni_list(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $dep10 = $repo->byDepartamento(10)->pluck('codigo')->sort()->values()->all();
        $this->assertSame(['G1', 'G2'], $dep10);

        $tutores = $repo->tutoresDniList();
        sort($tutores);
        $this->assertSame(['P0', 'P1'], $tutores);
    }

    public function test_with_acta_pendiente_retornar_nom_sols_pendents(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $codigos = $repo->withActaPendiente()->pluck('codigo')->all();

        $this->assertSame(['G2'], $codigos);
    }

    public function test_by_tutor_or_substitute_retornar_grup_associat(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $grupo = $repo->byTutorOrSubstitute('P1', 'P0');
        $this->assertNotNull($grupo);
        $this->assertSame('G1', (string) $grupo->codigo);

        $grupoSustituit = $repo->byTutorOrSubstitute('PX', 'P0');
        $this->assertNotNull($grupoSustituit);
        $this->assertSame('G2', (string) $grupoSustituit->codigo);
    }

    public function test_with_students_retornar_sols_grups_amb_alumnat(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $codigos = $repo->withStudents()->pluck('codigo')->sort()->values()->all();
        $this->assertSame(['G1', 'G2'], $codigos);
    }

    public function test_first_by_tutor_dual_i_by_codes(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $dual = $repo->firstByTutorDual('PD');
        $this->assertNotNull($dual);
        $this->assertSame('G2', (string) $dual->codigo);

        $codigos = $repo->byCodes(['G1', 'G3'])->pluck('codigo')->sort()->values()->all();
        $this->assertSame(['G1', 'G3'], $codigos);
    }

    public function test_reassign_tutor_actualitza_grups(): void
    {
        $repo = $this->app->make(GrupoRepositoryInterface::class);

        $updated = $repo->reassignTutor('P1', 'P9');

        $this->assertSame(1, $updated);
        $this->assertSame('P9', DB::table('grupos')->where('codigo', 'G1')->value('tutor'));
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('sustituye_a', 10)->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('ciclos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('departamento')->nullable();
            $table->string('ciclo')->nullable();
            $table->string('normativa')->nullable();
            $table->unsignedTinyInteger('tipo')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('tutor')->nullable();
            $table->string('tutorDual')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('curso')->nullable();
            $table->unsignedInteger('acta_pendiente')->default(0);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });
    }

    private function seedData(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P0', 'sustituye_a' => null, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P1', 'sustituye_a' => 'P0', 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P2', 'sustituye_a' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('ciclos')->insert([
            ['id' => 1, 'departamento' => 10, 'ciclo' => 'C1', 'normativa' => 'LOE', 'tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'departamento' => 20, 'ciclo' => 'C2', 'normativa' => 'LOE', 'tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup 1', 'tutor' => 'P1', 'tutorDual' => null, 'idCiclo' => 1, 'curso' => 2, 'acta_pendiente' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G2', 'nombre' => 'Grup 2', 'tutor' => 'P0', 'tutorDual' => 'PD', 'idCiclo' => 1, 'curso' => 2, 'acta_pendiente' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'G3', 'nombre' => 'Grup 3', 'tutor' => 'BAJA', 'tutorDual' => null, 'idCiclo' => 2, 'curso' => 1, 'acta_pendiente' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('alumnos')->insert([
            ['nia' => 'A1', 'created_at' => now(), 'updated_at' => now()],
            ['nia' => 'A2', 'created_at' => now(), 'updated_at' => now()],
            ['nia' => 'A3', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A1', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A2', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A3', 'idGrupo' => 'G2'],
        ]);
    }
}
