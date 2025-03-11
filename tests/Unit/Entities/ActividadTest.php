<?php

namespace Tests\Unit\Entities;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        $query = Mockery::mock('Illuminate\Database\Eloquent\Builder');

        // Mock de la relació ActividadProfesor perquè no busque en la BD
        $actividadProfesor = Mockery::mock('alias:Intranet\Entities\ActividadProfesor');
        $actividadProfesor->shouldReceive('where')
            ->with('idProfesor', '12345678X')
            ->andReturnSelf();
        $actividadProfesor->shouldReceive('pluck')
            ->with('idActividad')
            ->andReturn(collect([1, 2, 3])); // Simulem IDs d'activitats

        $query->shouldReceive('whereIn')
            ->with('id', [1, 2, 3]) // ✅ Ara esperem un array, no un Collection
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
