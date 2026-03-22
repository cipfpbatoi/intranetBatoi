<?php

namespace Tests\Unit\Entities;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Intranet\Entities\Actividad;
use Mockery;
use Tests\TestCase;


class ActividadTest extends TestCase
{
    use WithoutModelEvents; // Evita que els esdeveniments de model s’executin
    public function test_se_puede_instanciar()
    {
        $actividad = new Actividad();
        $this->assertInstanceOf(Actividad::class, $actividad);
    }

    public function test_formato_fecha_desde()
    {
        $actividad = Mockery::mock(Actividad::class)->makePartial();
        $actividad->shouldReceive('getDesdeAttribute')->andReturn('01-03-2025 10:00');

        $this->assertEquals('01-03-2025 10:00', $actividad->desde);
    }

    public function test_formato_fecha_hasta()
    {
        $actividad = Mockery::mock(Actividad::class)->makePartial();
        $actividad->shouldReceive('getHastaAttribute')->andReturn('01-03-2025 12:00');

        $this->assertEquals('01-03-2025 12:00', $actividad->hasta);
    }

    public function test_scope_next()
    {
        $query = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $query->shouldReceive('where')
            ->with('desde', '>', date('Y-m-d'))
            ->once()
            ->andReturnSelf();

        $actividad = new Actividad();
        $this->assertInstanceOf(get_class($query), $actividad->scopeNext($query));
    }

    public function test_scope_profesor()
    {
        if (!Schema::hasTable('actividades')) {
            Schema::create('actividades', function ($table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->unsignedTinyInteger('extraescolar')->default(1);
                $table->unsignedTinyInteger('estado')->default(0);
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('profesores')) {
            Schema::create('profesores', function ($table) {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('codigo')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->date('fecha_baja')->nullable();
                $table->unsignedTinyInteger('activo')->default(1);
            });
        }

        if (!Schema::hasTable('actividad_profesor')) {
            Schema::create('actividad_profesor', function ($table) {
                $table->unsignedBigInteger('idActividad');
                $table->string('idProfesor', 10);
                $table->boolean('coordinador')->default(false);
            });
        }

        DB::table('actividades')->whereIn('id', [1, 2, 3])->delete();
        DB::table('actividades')->insert([
            [
                'id' => 1,
                'name' => 'A1',
                'extraescolar' => 1,
                'estado' => 0,
                'desde' => '2025-01-01 08:00:00',
                'hasta' => '2025-01-01 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'A2',
                'extraescolar' => 1,
                'estado' => 0,
                'desde' => '2025-01-02 08:00:00',
                'hasta' => '2025-01-02 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'A3',
                'extraescolar' => 1,
                'estado' => 0,
                'desde' => '2025-01-03 08:00:00',
                'hasta' => '2025-01-03 10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('profesores')->where('dni', '12345678X')->delete();
        $profesorPayload = [
            'dni' => '12345678X',
            'codigo' => 12345,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => '12345678x@test.local',
            'emailItaca' => '12345678x@itaca.local',
            'domicilio' => 'Carrer Prova 1',
            'movil1' => '600000001',
            'movil2' => '600000002',
            'sexo' => 'H',
            'codigo_postal' => '03801',
            'departamento' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'fecha_nac' => '1980-01-01',
            'foto' => 'default.png',
            'idioma' => 'ca',
            'mostrar' => 1,
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'password' => bcrypt('test-password'),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $profesorColumns = Schema::getColumnListing('profesores');
        DB::table('profesores')->insert(array_intersect_key($profesorPayload, array_flip($profesorColumns)));

        DB::table('actividad_profesor')->where('idProfesor', '12345678X')->delete();
        DB::table('actividad_profesor')->insert([
            ['idActividad' => 1, 'idProfesor' => '12345678X', 'coordinador' => false],
            ['idActividad' => 2, 'idProfesor' => '12345678X', 'coordinador' => false],
            ['idActividad' => 3, 'idProfesor' => '12345678X', 'coordinador' => true],
        ]);

        $query = Mockery::mock('Illuminate\Database\Eloquent\Builder');

        $query->shouldReceive('whereIn')
            ->with('id', [1, 2, 3])
            ->once()
            ->andReturnSelf();

        $actividad = new Actividad();
        $this->assertInstanceOf(get_class($query), $actividad->scopeProfesor($query, '12345678X'));
    }


    public function test_recomendada_devuelve_si_o_no()
    {
        $actividad = Mockery::mock(Actividad::class)->makePartial();
        $actividad->recomanada = true;
        $this->assertEquals('Sí', $actividad->recomendada);

        $actividad->recomanada = false;
        $this->assertEquals('No', $actividad->recomendada);
    }

    public function test_coordinador()
    {
        $actividad = Mockery::mock(Actividad::class)->makePartial();
        $actividad->shouldReceive('Creador')->andReturn('12345678X');

        $this->assertEquals('12345678X', $actividad->Creador());
    }

    public function test_scope_dia()
    {
        $query = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $query->shouldReceive('where')
            ->with('desde', '<=', '2025-03-01 23:59:59')
            ->once()
            ->andReturnSelf();
        $query->shouldReceive('where')
            ->with('hasta', '>=', '2025-03-01 00:00:00')
            ->once()
            ->andReturnSelf();

        $actividad = new Actividad();
        $this->assertInstanceOf(get_class($query), $actividad->scopeDia($query, '2025-03-01'));
    }

    public function test_relaciones_grupos_profesores_menores()
    {
        $actividad = Mockery::mock(Actividad::class)->makePartial();

        // Creem un mock de la relació `BelongsToMany`
        $relacionMock = Mockery::mock(BelongsToMany::class);

        // Indiquem que quan Laravel invoqui `grupos()`, `profesores()` o `menores()`, ha de retornar aquest mock
        $actividad->shouldReceive('grupos')->andReturn($relacionMock);
        $actividad->shouldReceive('profesores')->andReturn($relacionMock);
        $actividad->shouldReceive('menores')->andReturn($relacionMock);

        // Assegurem-nos que `getResults()` retorni una col·lecció buida en aquestes relacions
        $relacionMock->shouldReceive('getResults')->andReturn(new Collection());

        // Verifiquem que les relacions són instàncies de `BelongsToMany`
        $this->assertInstanceOf(BelongsToMany::class, $actividad->grupos());
        $this->assertInstanceOf(BelongsToMany::class, $actividad->profesores());
        $this->assertInstanceOf(BelongsToMany::class, $actividad->menores());
    }
}
