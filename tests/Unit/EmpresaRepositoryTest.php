<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Empresa\EmpresaRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Empresa\EloquentEmpresaRepository;
use Tests\TestCase;

class EmpresaRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('empresa_repository_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('centros_instructores');
        Schema::connection('sqlite')->dropIfExists('instructores');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('centros');
        Schema::connection('sqlite')->dropIfExists('ciclos');
        Schema::connection('sqlite')->dropIfExists('empresas');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(EmpresaRepositoryInterface::class);

        $this->assertInstanceOf(EloquentEmpresaRepository::class, $repo);
    }

    public function test_list_for_grid_i_find_for_show(): void
    {
        $repo = $this->app->make(EmpresaRepositoryInterface::class);

        $grid = $repo->listForGrid();
        $this->assertCount(3, $grid);
        $this->assertSame('Empresa A', (string) $grid->first()->nombre);

        $empresa = $repo->findForShow(1);
        $this->assertSame(1, (int) $empresa->id);
        $this->assertTrue($empresa->relationLoaded('centros'));
        $this->assertTrue($empresa->centros->first()->relationLoaded('colaboraciones'));
        $this->assertTrue($empresa->centros->first()->relationLoaded('instructores'));
    }

    public function test_colaboracion_ids_i_cycles_by_department(): void
    {
        $repo = $this->app->make(EmpresaRepositoryInterface::class);

        $ids = $repo->colaboracionIdsByCycleAndCenters(10, [100, 200])->all();
        sort($ids);
        $this->assertSame([500, 501], $ids);

        $cycles = $repo->cyclesByDepartment('D001');
        $this->assertCount(1, $cycles);
        $this->assertSame(10, (int) $cycles->first()->id);
    }

    public function test_convenio_social_i_erasmus_lists(): void
    {
        $repo = $this->app->make(EmpresaRepositoryInterface::class);

        $convenio = $repo->convenioList()->pluck('id')->all();
        $this->assertSame([1], $convenio);

        $social = $repo->socialConcertList()->pluck('id')->all();
        $this->assertSame([2, 3], $social);

        $erasmus = $repo->erasmusList()->pluck('id')->all();
        $this->assertSame([3], $erasmus);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('empresas', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('concierto')->nullable();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('cif')->nullable();
            $table->string('actividad')->nullable();
            $table->unsignedTinyInteger('europa')->default(0);
            $table->string('fichero')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('ciclos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('departamento')->nullable();
            $table->string('ciclo')->nullable();
            $table->string('normativa')->nullable();
            $table->unsignedTinyInteger('tipo')->nullable();
        });

        Schema::connection('sqlite')->create('centros', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idEmpresa');
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idCentro');
            $table->unsignedInteger('idCiclo');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('centros_instructores', function (Blueprint $table): void {
            $table->unsignedInteger('idCentro');
            $table->string('idInstructor');
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        DB::table('empresas')->insert([
            [
                'id' => 1,
                'concierto' => '1001',
                'nombre' => 'Empresa A',
                'direccion' => 'Dir A',
                'localidad' => 'Loc A',
                'telefono' => '111',
                'email' => 'a@example.com',
                'cif' => 'CIFA',
                'actividad' => 'Act A',
                'europa' => 0,
                'fichero' => 'f1.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'concierto' => null,
                'nombre' => 'Empresa B',
                'direccion' => 'Dir B',
                'localidad' => 'Loc B',
                'telefono' => '222',
                'email' => 'b@example.com',
                'cif' => 'CIFB',
                'actividad' => 'Act B',
                'europa' => 0,
                'fichero' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'concierto' => null,
                'nombre' => 'Empresa C',
                'direccion' => 'Dir C',
                'localidad' => 'Loc C',
                'telefono' => '333',
                'email' => 'c@example.com',
                'cif' => 'CIFC',
                'actividad' => 'Act C',
                'europa' => 1,
                'fichero' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('ciclos')->insert([
            ['id' => 10, 'departamento' => 'D001', 'ciclo' => 'CF1', 'normativa' => 'LOE', 'tipo' => 2],
            ['id' => 20, 'departamento' => 'D002', 'ciclo' => 'CF2', 'normativa' => 'LOE', 'tipo' => 2],
        ]);

        DB::table('centros')->insert([
            ['id' => 100, 'idEmpresa' => 1, 'nombre' => 'Centre A1', 'direccion' => 'DA1', 'localidad' => 'LA1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 200, 'idEmpresa' => 1, 'nombre' => 'Centre A2', 'direccion' => 'DA2', 'localidad' => 'LA2', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 300, 'idEmpresa' => 2, 'nombre' => 'Centre B1', 'direccion' => 'DB1', 'localidad' => 'LB1', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('colaboraciones')->insert([
            ['id' => 500, 'idCentro' => 100, 'idCiclo' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 501, 'idCentro' => 200, 'idCiclo' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 502, 'idCentro' => 300, 'idCiclo' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('instructores')->insert([
            ['dni' => 'I001', 'nombre' => 'Inst 1', 'email' => 'i1@example.com', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('centros_instructores')->insert([
            ['idCentro' => 100, 'idInstructor' => 'I001', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
