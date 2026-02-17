<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Expediente\ExpedienteRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Expediente\EloquentExpedienteRepository;
use Tests\TestCase;

class ExpedienteRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('expediente_repository_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('expedientes');
        Schema::connection('sqlite')->dropIfExists('tipo_expedientes');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(ExpedienteRepositoryInterface::class);

        $this->assertInstanceOf(EloquentExpedienteRepository::class, $repo);
    }

    public function test_pending_ready_and_types_queries(): void
    {
        $repo = $this->app->make(ExpedienteRepositoryInterface::class);

        $pending = $repo->pendingAuthorization()->pluck('id')->all();
        $this->assertSame([1], $pending);

        $ready = $repo->readyToPrint()->pluck('id')->all();
        $this->assertSame([2], $ready);

        $types = $repo->allTypes()->pluck('id')->all();
        $this->assertSame([10, 20], $types);
    }

    public function test_find_and_find_or_fail(): void
    {
        $repo = $this->app->make(ExpedienteRepositoryInterface::class);

        $this->assertNotNull($repo->find(1));
        $this->assertSame(1, (int) $repo->findOrFail(1)->id);
        $this->assertNull($repo->find(9999));
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('tipo_expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('titulo')->nullable();
            $table->unsignedTinyInteger('orientacion')->default(0);
            $table->unsignedTinyInteger('informe')->default(0);
            $table->unsignedInteger('rol')->default(1);
        });

        Schema::connection('sqlite')->create('expedientes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('tipo')->nullable();
            $table->string('idModulo')->nullable();
            $table->string('idAlumno')->nullable();
            $table->string('idProfesor')->nullable();
            $table->text('explicacion')->nullable();
            $table->date('fecha')->nullable();
            $table->date('fechatramite')->nullable();
            $table->tinyInteger('estado')->default(0);
        });
    }

    private function seedData(): void
    {
        DB::table('tipo_expedientes')->insert([
            ['id' => 10, 'titulo' => 'Tipus A', 'orientacion' => 0, 'informe' => 0, 'rol' => 1],
            ['id' => 20, 'titulo' => 'Tipus B', 'orientacion' => 1, 'informe' => 1, 'rol' => 1],
        ]);

        DB::table('expedientes')->insert([
            [
                'id' => 1,
                'tipo' => 10,
                'idAlumno' => 'A001',
                'idProfesor' => 'P001',
                'explicacion' => 'Pendiente',
                'fecha' => '2026-02-12',
                'estado' => 1,
            ],
            [
                'id' => 2,
                'tipo' => 20,
                'idAlumno' => 'A002',
                'idProfesor' => 'P002',
                'explicacion' => 'Listo',
                'fecha' => '2026-02-13',
                'estado' => 2,
            ],
        ]);
    }
}

