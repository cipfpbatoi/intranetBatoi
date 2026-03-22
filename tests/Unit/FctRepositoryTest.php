<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Fct\FctRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Fct\EloquentFctRepository;
use Tests\TestCase;

class FctRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('fct_repository_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('instructores');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('centros');
        Schema::connection('sqlite')->dropIfExists('empresas');
        Schema::connection('sqlite')->dropIfExists('fcts');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(FctRepositoryInterface::class);
        $this->assertInstanceOf(EloquentFctRepository::class, $repo);
    }

    public function test_find_signature_attach_detach_and_empresa_id(): void
    {
        $repo = $this->app->make(FctRepositoryInterface::class);

        $fct = $repo->findOrFail(1);
        $this->assertSame(1, (int) $fct->id);

        $signature = $repo->firstByColaboracionAsociacionInstructor(10, 1, 'I001');
        $this->assertNotNull($signature);
        $this->assertSame(1, (int) $signature->id);

        $repo->attachAlumno(1, 'A001', ['horas' => 400, 'desde' => '2026-03-01', 'hasta' => '2026-06-01']);
        $this->assertSame(1, DB::table('alumno_fcts')->where('idFct', 1)->where('idAlumno', 'A001')->count());

        $repo->detachAlumno(1, 'A001');
        $this->assertSame(0, DB::table('alumno_fcts')->where('idFct', 1)->where('idAlumno', 'A001')->count());

        $empresaId = $repo->empresaIdByFct(1);
        $this->assertSame(100, $empresaId);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion')->nullable();
            $table->string('idInstructor')->nullable();
            $table->unsignedTinyInteger('asociacion')->default(1);
            $table->unsignedTinyInteger('autorizacion')->default(0);
            $table->unsignedTinyInteger('erasmus')->default(0);
            $table->string('cotutor')->nullable();
            $table->unsignedTinyInteger('correoInstructor')->default(0);
        });

        Schema::connection('sqlite')->create('empresas', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('nombre')->nullable();
        });

        Schema::connection('sqlite')->create('centros', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idEmpresa')->nullable();
            $table->string('nombre')->nullable();
        });

        Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idCentro')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->string('tutor')->nullable();
        });

        Schema::connection('sqlite')->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();
        });

        Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idFct');
            $table->string('idAlumno');
            $table->unsignedInteger('horas')->nullable();
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
        });
    }

    private function seedData(): void
    {
        DB::table('empresas')->insert([
            'id' => 100,
            'nombre' => 'Empresa Test',
        ]);

        DB::table('centros')->insert([
            'id' => 50,
            'idEmpresa' => 100,
            'nombre' => 'Centre Test',
        ]);

        DB::table('colaboraciones')->insert([
            'id' => 10,
            'idCentro' => 50,
            'idCiclo' => 1,
            'tutor' => 'P001',
        ]);

        DB::table('instructores')->insert([
            'dni' => 'I001',
            'nombre' => 'Instr Test',
            'email' => 'i001@test.local',
        ]);

        DB::table('fcts')->insert([
            'id' => 1,
            'idColaboracion' => 10,
            'idInstructor' => 'I001',
            'asociacion' => 1,
            'autorizacion' => 0,
            'erasmus' => 0,
            'cotutor' => null,
            'correoInstructor' => 0,
        ]);
    }
}

