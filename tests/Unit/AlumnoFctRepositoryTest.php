<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\AlumnoFct\EloquentAlumnoFctRepository;
use Tests\TestCase;

class AlumnoFctRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('alumnofct_repository_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(AlumnoFctRepositoryInterface::class);

        $this->assertInstanceOf(EloquentAlumnoFctRepository::class, $repo);
    }

    public function test_find_i_find_or_fail(): void
    {
        $repo = $this->app->make(AlumnoFctRepositoryInterface::class);

        $found = $repo->find(1);
        $this->assertNotNull($found);
        $this->assertSame(1, (int) $found->id);

        $foundOrFail = $repo->findOrFail(2);
        $this->assertSame(2, (int) $foundOrFail->id);
    }

    public function test_all_retorna_tots_els_registres(): void
    {
        $repo = $this->app->make(AlumnoFctRepositoryInterface::class);

        $all = $repo->all();
        $this->assertCount(3, $all);
    }

    public function test_by_grupo_es_fct_i_by_grupo_es_dual_filtren_per_cicle_i_tipus(): void
    {
        $repo = $this->app->make(AlumnoFctRepositoryInterface::class);

        $esFct = $repo->byGrupoEsFct('G1')->pluck('id')->values()->all();
        $this->assertSame([1], $esFct);

        $esDual = $repo->byGrupoEsDual('G1')->pluck('id')->values()->all();
        $this->assertSame([2], $esDual);
    }

    public function test_reassign_profesor_actualitza_registres(): void
    {
        $repo = $this->app->make(AlumnoFctRepositoryInterface::class);

        $updated = $repo->reassignProfesor('P1', 'P9');

        $this->assertSame(3, $updated);
        $this->assertSame(3, DB::table('alumno_fcts')->where('idProfesor', 'P9')->count());
    }

    public function test_first_by_id_sao_i_by_alumno(): void
    {
        DB::table('alumno_fcts')->where('id', 2)->update(['idSao' => 'SAO-22']);
        DB::table('alumno_fcts')->where('id', 1)->update(['a56' => 1]);
        DB::table('alumno_fcts')->where('id', 2)->update(['a56' => 2]);
        DB::table('alumno_fcts')->where('id', 3)->update(['a56' => 0]);

        $repo = $this->app->make(AlumnoFctRepositoryInterface::class);

        $bySao = $repo->firstByIdSao('SAO-22');
        $this->assertNotNull($bySao);
        $this->assertSame(2, (int) $bySao->id);

        $byAlumno = $repo->byAlumno('A1')->pluck('id')->sort()->values()->all();
        $this->assertSame([1, 2], $byAlumno);

        $byAlumnoA56 = $repo->byAlumnoWithA56('A1')->pluck('id')->sort()->values()->all();
        $this->assertSame([1, 2], $byAlumnoA56);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno');
            $table->unsignedInteger('idFct');
            $table->string('idProfesor')->nullable();
            $table->string('idSao')->nullable();
            $table->unsignedTinyInteger('a56')->default(0);
        });

        Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idColaboracion');
            $table->unsignedTinyInteger('asociacion')->default(1);
            $table->unsignedTinyInteger('erasmus')->default(0);
        });

        Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idCiclo');
        });

        Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->unsignedInteger('idCiclo');
        });

        Schema::connection('sqlite')->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });
    }

    private function seedData(): void
    {
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'idCiclo' => 10],
            ['codigo' => 'G2', 'idCiclo' => 20],
        ]);

        DB::table('colaboraciones')->insert([
            ['id' => 100, 'idCiclo' => 10],
            ['id' => 200, 'idCiclo' => 20],
        ]);

        DB::table('fcts')->insert([
            ['id' => 1, 'idColaboracion' => 100, 'asociacion' => 1, 'erasmus' => 0],
            ['id' => 2, 'idColaboracion' => 100, 'asociacion' => 3, 'erasmus' => 0],
            ['id' => 3, 'idColaboracion' => 200, 'asociacion' => 1, 'erasmus' => 0],
        ]);

        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A1', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A2', 'idGrupo' => 'G1'],
        ]);

        DB::table('alumno_fcts')->insert([
            ['id' => 1, 'idAlumno' => 'A1', 'idFct' => 1, 'idProfesor' => 'P1'],
            ['id' => 2, 'idAlumno' => 'A1', 'idFct' => 2, 'idProfesor' => 'P1'],
            ['id' => 3, 'idAlumno' => 'A2', 'idFct' => 3, 'idProfesor' => 'P1'],
        ]);
    }
}
