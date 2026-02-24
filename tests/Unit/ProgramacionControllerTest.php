<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\ProgramacionController;
use Tests\TestCase;

class ProgramacionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        config(['auth.defaults.guard' => 'profesor']);

        config()->set('constants.modulosSinProgramacion', ['TUT']);
        config()->set('constants.modulosNoLectivos', ['TUT']);

        $this->createSchema();
    }

    public function test_search_retorna_programacions_del_professor_amb_relacions_carregades(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P1',
            'nombre' => 'Prova',
            'apellido1' => 'Tutor',
            'apellido2' => 'Unit',
            'email' => 'p1@test.local',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
        ]);

        DB::table('ciclos')->insert([
            ['id' => 1, 'cliteral' => 'Cicle 1', 'vliteral' => 'Cicle 1', 'ciclo' => 'CF1'],
        ]);

        DB::table('modulos')->insert([
            ['codigo' => 'M1', 'cliteral' => 'Modulo 1', 'vliteral' => 'Modul 1'],
            ['codigo' => 'M2', 'cliteral' => 'Modulo 2', 'vliteral' => 'Modul 2'],
        ]);

        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'idCiclo' => 1, 'nombre' => 'Grup 1'],
        ]);

        DB::table('modulo_ciclos')->insert([
            ['id' => 10, 'idModulo' => 'M1', 'idCiclo' => 1, 'idDepartamento' => 20],
            ['id' => 11, 'idModulo' => 'M2', 'idCiclo' => 1, 'idDepartamento' => 20],
        ]);

        DB::table('programaciones')->insert([
            ['id' => 1, 'idModuloCiclo' => 10, 'fichero' => 'm1.pdf'],
            ['id' => 2, 'idModuloCiclo' => 11, 'fichero' => 'm2.pdf'],
        ]);

        DB::table('horarios')->insert([
            ['idProfesor' => 'P1', 'modulo' => 'M1', 'idGrupo' => 'G1'],
            ['idProfesor' => 'P1', 'modulo' => 'TUT', 'idGrupo' => 'G1'],
        ]);

        $this->actingAs(Profesor::findOrFail('P1'), 'profesor');

        $controller = new DummyProgramacionController();
        $result = $controller->publicSearch();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([1], $result->pluck('id')->all());
        $this->assertTrue($result->first()->relationLoaded('Ciclo'));
        $this->assertTrue($result->first()->relationLoaded('Modulo'));
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a', 10)->nullable();
            $table->date('fecha_baja')->nullable();
            $table->boolean('activo')->default(true);
        });

        $schema->create('programaciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloCiclo');
            $table->string('fichero')->nullable();
            $table->unsignedTinyInteger('estado')->default(0);
            $table->timestamps();
        });

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo');
            $table->unsignedInteger('idCiclo');
            $table->unsignedInteger('idDepartamento');
            $table->unsignedTinyInteger('curso')->default(1);
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->unsignedInteger('idCiclo');
            $table->string('turno')->nullable();
            $table->timestamps();
        });

        $schema->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor');
            $table->string('modulo')->nullable();
            $table->string('idGrupo')->nullable();
            $table->timestamps();
        });

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('ciclo')->nullable();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });
    }
}

class DummyProgramacionController extends ProgramacionController
{
    public function __construct()
    {
        // Evitem inicialització de UI en tests unitaris.
    }

    public function publicSearch()
    {
        return $this->search();
    }
}

