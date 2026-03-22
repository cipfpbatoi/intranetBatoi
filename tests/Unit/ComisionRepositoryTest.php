<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Comision\ComisionRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Comision\EloquentComisionRepository;
use Tests\TestCase;

class ComisionRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('comision_repository_testing.sqlite');
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
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('comision_fcts');
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(ComisionRepositoryInterface::class);

        $this->assertInstanceOf(EloquentComisionRepository::class, $repo);
    }

    public function test_by_day_i_with_profesor_by_day(): void
    {
        $this->seedProfesor('P001');
        $id = $this->seedComision([
            'idProfesor' => 'P001',
            'desde' => '2026-02-14 09:00:00',
            'hasta' => '2026-02-14 12:00:00',
            'estado' => 1,
        ]);
        $this->seedComision([
            'idProfesor' => 'P001',
            'desde' => '2026-02-15 09:00:00',
            'hasta' => '2026-02-15 12:00:00',
            'estado' => 1,
        ]);

        $repo = $this->app->make(ComisionRepositoryInterface::class);

        $dayItems = $repo->byDay('2026-02-14');
        $this->assertCount(1, $dayItems);
        $this->assertSame($id, (int) $dayItems->first()->id);

        $withProfesor = $repo->withProfesorByDay('2026-02-14');
        $this->assertTrue($withProfesor->first()->relationLoaded('profesor'));
        $this->assertSame('P001', (string) $withProfesor->first()->idProfesor);
    }

    public function test_authorize_all_pending_i_set_estado(): void
    {
        $this->seedComision(['estado' => 1]);
        $id = $this->seedComision(['estado' => 1]);
        $this->seedComision(['estado' => 2]);

        $repo = $this->app->make(ComisionRepositoryInterface::class);

        $updated = $repo->authorizeAllPending();
        $this->assertSame(2, $updated);
        $this->assertSame(2, (int) DB::table('comisiones')->where('id', $id)->value('estado'));

        $repo->setEstado($id, 5);
        $this->assertSame(5, (int) DB::table('comisiones')->where('id', $id)->value('estado'));
    }

    public function test_prepay_by_profesor_i_pending_unpaid(): void
    {
        $this->seedComision([
            'idProfesor' => 'P100',
            'estado' => 4,
            'comida' => 10,
            'gastos' => 0,
            'alojamiento' => 0,
            'kilometraje' => 0,
        ]);
        $this->seedComision([
            'idProfesor' => 'P100',
            'estado' => 1,
            'comida' => 0,
            'gastos' => 15,
            'alojamiento' => 0,
            'kilometraje' => 0,
        ]);

        $repo = $this->app->make(ComisionRepositoryInterface::class);

        $this->assertTrue($repo->hasPendingUnpaidByProfesor('P100'));

        $prePayItems = $repo->prePayByProfesor('P100');
        $this->assertCount(1, $prePayItems);
        $this->assertSame(6, (int) DB::table('comisiones')
            ->where('idProfesor', 'P100')
            ->where('comida', 10)
            ->value('estado'));
    }

    public function test_attach_i_detach_fct(): void
    {
        $comisionId = $this->seedComision([
            'idProfesor' => 'P001',
            'estado' => 1,
        ]);
        DB::table('fcts')->insert([
            'id' => 1001,
            'idColaboracion' => 1,
            'asociacion' => 1,
        ]);

        $repo = $this->app->make(ComisionRepositoryInterface::class);

        $repo->attachFct($comisionId, 1001, '09:00:00', true);
        $this->assertSame(1, DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1001)
            ->count());
        $this->assertSame(1, (int) DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1001)
            ->value('aviso'));

        $repo->detachFct($comisionId, 1001);
        $this->assertSame(0, DB::table('comision_fcts')
            ->where('idComision', $comisionId)
            ->where('idFct', 1001)
            ->count());
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('comisiones')) {
            Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10)->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->unsignedTinyInteger('fct')->default(0);
                $table->text('servicio')->nullable();
                $table->decimal('alojamiento', 8, 2)->default(0);
                $table->decimal('comida', 8, 2)->default(0);
                $table->decimal('gastos', 8, 2)->default(0);
                $table->unsignedInteger('kilometraje')->default(0);
                $table->unsignedTinyInteger('medio')->default(0);
                $table->string('marca')->nullable();
                $table->string('matricula')->nullable();
                $table->text('itinerario')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('fcts')) {
            Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('idColaboracion')->nullable();
                $table->unsignedTinyInteger('asociacion')->default(1);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('comision_fcts')) {
            Schema::connection('sqlite')->create('comision_fcts', function (Blueprint $table): void {
                $table->unsignedBigInteger('idComision');
                $table->unsignedBigInteger('idFct');
                $table->time('hora_ini')->nullable();
                $table->unsignedTinyInteger('aviso')->default(0);
            });
        }
    }

    private function seedProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
        ]);
    }

    private function seedComision(array $override = []): int
    {
        $data = array_merge([
            'idProfesor' => 'P001',
            'desde' => '2026-02-14 09:00:00',
            'hasta' => '2026-02-14 12:00:00',
            'fct' => 0,
            'servicio' => 'Servei',
            'alojamiento' => 0,
            'comida' => 0,
            'gastos' => 0,
            'kilometraje' => 0,
            'medio' => 0,
            'marca' => null,
            'matricula' => null,
            'itinerario' => null,
            'estado' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ], $override);

        return (int) DB::table('comisiones')->insertGetId($data);
    }
}
