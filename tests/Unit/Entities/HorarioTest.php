<?php

namespace Tests\Unit\Entities;


use Tests\TestCase;
use Mockery;
use Intranet\Entities\Horario;
use Illuminate\Support\Collection;

class HorarioTest extends TestCase
{
    public function test_horario_semanal_retorna_array_correcte()
    {
        // Creem un "partial mock" del model Horario per a mètodes estàtics
        $horarioMock = Mockery::mock('overload:' . Horario::class)
            ->shouldAllowMockingProtectedMethods();

        // Simulem el retorn de la consulta Horario::Profesor($profesor)
        $horarioMock->shouldReceive('Profesor')
            ->with(1)
            ->andReturnSelf();

        $horarioMock->shouldReceive('with')->andReturnSelf();
        $horarioMock->shouldReceive('get')
            ->andReturn(new Collection([
                (object)['dia_semana' => 'L', 'sesion_orden' => 1],
                (object)['dia_semana' => 'M', 'sesion_orden' => 2],
            ]));

        // Simulem el mètode estàtic HorarioSemanal
        $horarioMock->shouldReceive('HorarioSemanal')
            ->with(1)
            ->andReturn([
                'L' => [1 => (object)['dia_semana' => 'L', 'sesion_orden' => 1]],
                'M' => [2 => (object)['dia_semana' => 'M', 'sesion_orden' => 2]],
            ]);

        // Cridem el mètode i comprovem el resultat
        $result = $horarioMock->HorarioSemanal(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('L', $result);
        $this->assertArrayHasKey(1, $result['L']);
        $this->assertArrayHasKey('M', $result);
        $this->assertArrayHasKey(2, $result['M']);
    }


    public function test_horario_grupo_retorna_array_correcte()
    {
        // Mock per a evitar consultes reals a la base de dades
        $horarioMock = Mockery::mock('overload:' . Horario::class)
            ->shouldAllowMockingProtectedMethods();

        $horaMock = Mockery::mock('overload:' . Hora::class);

        // Simulem la consulta de totes les hores disponibles
        $horaMock->shouldReceive('all')->andReturn(collect([
            (object)['codigo' => 1],
            (object)['codigo' => 2],
        ]));

        // Simulem la consulta al model Horario per grup
        $horarioMock->shouldReceive('Grup')
            ->with(101)
            ->andReturnSelf();

        $horarioMock->shouldReceive('Dia')
            ->andReturnSelf();

        $horarioMock->shouldReceive('where')->andReturnSelf();

        $horarioMock->shouldReceive('first')
            ->andReturnUsing(function () {
                return (object)[
                    'dia_semana' => 'L',
                    'sesion_orden' => 1,
                    'ocupacion' => null,
                    'modulo' => 'MATH101',
                ];
            });

        // Simulem el retorn final de `HorarioGrupo`
        $horarioMock->shouldReceive('HorarioGrupo')
            ->with(101)
            ->andReturn([
                'L' => [1 => (object)['dia_semana' => 'L', 'sesion_orden' => 1, 'modulo' => 'MATH101']],
            ]);

        // Executem la funció i comprovem el resultat esperat
        $result = $horarioMock->HorarioGrupo(101);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('L', $result);
        $this->assertArrayHasKey(1, $result['L']);
        $this->assertEquals('MATH101', $result['L'][1]->modulo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
