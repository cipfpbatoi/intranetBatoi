<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Profesor\ProfesorRepositoryInterface;
use Intranet\Infrastructure\Persistence\Eloquent\Profesor\EloquentProfesorRepository;
use Tests\TestCase;

class ProfesorRepositoryTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('profesor_repository_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('horarios');
        Schema::connection('sqlite')->dropIfExists('ocupaciones');
        Schema::connection('sqlite')->dropIfExists('modulos');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('departamentos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_binding_resol_repositori_eloquent(): void
    {
        $repo = $this->app->make(ProfesorRepositoryInterface::class);

        $this->assertInstanceOf(EloquentProfesorRepository::class, $repo);
    }

    public function test_activos_ordered_i_find_by_codigo(): void
    {
        $this->seedProfesor('P003', 'Zeta', 'Tres', 'A', 1, null, '300');
        $this->seedProfesor('P001', 'Alfa', 'U', 'A', 1, null, '100');
        $this->seedProfesor('P002', 'Beta', 'Dos', 'A', 0, null, '200');

        $repo = $this->app->make(ProfesorRepositoryInterface::class);

        $activos = $repo->activosOrdered();
        $this->assertCount(2, $activos);
        $this->assertSame('P003', (string) $activos->first()->dni);

        $byCodigo = $repo->findByCodigo('100');
        $this->assertNotNull($byCodigo);
        $this->assertSame('P001', (string) $byCodigo->dni);
    }

    public function test_by_dnis_i_find_by_sustituye(): void
    {
        $this->seedProfesor('P010', 'Nom', 'Un', 'A', 1, null, '010');
        $this->seedProfesor('P011', 'Nom', 'Dos', 'A', 1, 'P010', '011');
        $this->seedProfesor('P012', 'Nom', 'Tres', 'A', 1, null, '012');

        $repo = $this->app->make(ProfesorRepositoryInterface::class);

        $subset = $repo->byDnis(['P010', 'P012']);
        $this->assertCount(2, $subset);

        $substituto = $repo->findBySustituyeA('P010');
        $this->assertNotNull($substituto);
        $this->assertSame('P011', (string) $substituto->dni);
    }

    public function test_used_codigos_between_retornar_llista_de_codis_ocupats(): void
    {
        $this->seedProfesor('P020', 'Nom', 'A', 'A', 1, null, '1049');
        $this->seedProfesor('P021', 'Nom', 'B', 'B', 1, null, '1050');
        $this->seedProfesor('P022', 'Nom', 'C', 'C', 1, null, '3333');
        $this->seedProfesor('P023', 'Nom', 'D', 'D', 1, null, '9000');
        $this->seedProfesor('P024', 'Nom', 'E', 'E', 1, null, '9001');

        $repo = $this->app->make(ProfesorRepositoryInterface::class);
        $used = $repo->usedCodigosBetween(1050, 9000);

        sort($used);
        $this->assertSame([1050, 3333, 9000], $used);
    }

    public function test_plantilla_i_departamentos_amb_horari(): void
    {
        DB::table('departamentos')->insert([
            ['id' => 1, 'depcurt' => 'D1', 'didactico' => 1],
            ['id' => 2, 'depcurt' => 'D2', 'didactico' => 1],
        ]);

        $this->seedProfesor('P100', 'Nom', 'Ape1', 'Ape2', 1, null, '100', 1);
        $this->seedProfesor('P101', 'Nom', 'Ape3', 'Ape4', 1, null, '101', 2);

        DB::table('grupos')->insert(['codigo' => 'G1', 'nombre' => 'Grup 1']);
        DB::table('modulos')->insert(['codigo' => 'M1', 'cliteral' => 'M1', 'vliteral' => 'M1']);
        DB::table('ocupaciones')->insert(['codigo' => '90', 'nombre' => 'Guardia', 'nom' => 'Guardia']);
        DB::table('horarios')->insert([
            'idProfesor' => 'P100',
            'idGrupo' => 'G1',
            'dia_semana' => 'L',
            'sesion_orden' => 1,
            'ocupacion' => '90',
            'modulo' => 'M1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $repo = $this->app->make(ProfesorRepositoryInterface::class);

        $plantilla = $repo->plantillaOrderedWithDepartamento();
        $this->assertCount(2, $plantilla);
        $this->assertTrue($plantilla->first()->relationLoaded('Departamento'));

        $withHorario = $repo->activosByDepartamentosWithHorario([1], 'L', 1);
        $this->assertCount(1, $withHorario);
        $this->assertSame('P100', (string) $withHorario->first()->dni);
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('departamentos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('depcurt')->nullable();
            $table->unsignedTinyInteger('didactico')->default(1);
        });

        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('departamento')->nullable();
            $table->unsignedTinyInteger('activo')->default(1);
            $table->date('fecha_baja')->nullable();
            $table->string('sustituye_a', 10)->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
        });

        Schema::connection('sqlite')->create('modulos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        Schema::connection('sqlite')->create('ocupaciones', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->string('nom')->nullable();
        });

        Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('idGrupo')->nullable();
            $table->string('dia_semana');
            $table->unsignedInteger('sesion_orden');
            $table->string('ocupacion')->nullable();
            $table->string('modulo')->nullable();
            $table->timestamps();
        });
    }

    private function seedProfesor(
        string $dni,
        string $nombre,
        string $apellido1,
        string $apellido2,
        int $activo,
        ?string $sustituyeA,
        string $codigo,
        int|string $departamento = 1
    ): void {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'departamento' => (string) $departamento,
            'activo' => $activo,
            'fecha_baja' => null,
            'sustituye_a' => $sustituyeA,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
