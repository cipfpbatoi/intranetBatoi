<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Intranet\Componentes\Mensaje;
use Intranet\Entities\Grupo;
use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Intranet\Jobs\SendEmail;
use Intranet\Services\AdviseTeacher;
use Mockery;
use Tests\TestCase;


class AdviseTeacherTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        // Mocks per evitar connexió real a BD
        $this->mockHorario = Mockery::mock('alias:' . Horario::class);
        $this->mockHora = Mockery::mock('alias:' . Hora::class);
        $this->mockGrupo = Mockery::mock('alias:' . Grupo::class);
        $this->mockMensaje = Mockery::mock('alias:' . Mensaje::class);

        $this->mockHora->shouldReceive('horasAfectadas')
            ->andReturn(collect([1, 2, 3]));

        $this->mockHorario->shouldReceive('distinct')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('select')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('Profesor')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('Dia')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('GuardiaAll')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('whereNotNull')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('whereIn')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('where')
            ->andReturnSelf();

        $this->mockHorario->shouldReceive('groupBy')
            ->andReturnSelf();
    }

    public function test_exec_no_envia_missatges_si_no_hi_ha_grups_afectats()
    {
        $this->mockHorario->shouldReceive('get')
            ->andReturn(collect([]));

        $elemento = new \stdClass();
        $elemento->desde = '2025-03-01';
        $elemento->hasta = '2025-03-01';
        $elemento->idProfesor = 1;

        AdviseTeacher::exec($elemento);

        // ✅ Ara verifiquem que NO s'ha cridat `Mensaje::send()`
        $this->mockMensaje->shouldNotHaveReceived('send');
    }
     public function test_exec_envia_missatges_si_hi_ha_professors_afectats()
    {
        // Simulem que hi ha grups afectats

        // ✅ Simulem professors afectats amb `idProfesor = 1`
        $this->mockHorario->shouldReceive('get')
            ->andReturn(collect([(object)['idProfesor' => 1, 'idGrupo' => 1]]));

        // ✅ Ajustem `shouldReceive()` perquè `emisor` pugui ser `null`
        $this->mockMensaje->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($id) {
                return $id === 1; // Assegurem-nos que és `idProfesor = 1`
            }), Mockery::type('string'), '#', Mockery::any()); // Permetem `null` a `emisor`

        $elemento = json_decode(json_encode([
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => 3
        ]));

        AdviseTeacher::exec($elemento);
    }
     public function test_gruposAfectados_retornara_una_colleccio()
    {
        $elemento = (object)[
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => 1,
            'idGrupo' => 1,
        ];

        $this->mockHorario->shouldReceive('distinct->select->Profesor->whereNotNull->get')
            ->andReturn(collect([(object)['idGrupo' => 1]]));

        $resultat = AdviseTeacher::gruposAfectados($elemento, 1);
        $this->assertInstanceOf(Collection::class, $resultat);
    }

    public function test_sendEmailTutor_enviar_email_si_hi_ha_tutor()
    {
        Queue::fake(); // Simulem la cua de treballs

        $elemento = (object)[
            'desde' => '2025-03-01',
            'hasta' => '2025-03-01',
            'idProfesor' => 1,
            'idGrupo' => 1 // ✅ Afegim aquesta propietat
        ];

        // Simulem grups afectats
        $this->mockHorario->shouldReceive('distinct->select->Profesor->whereNotNull->get')
            ->andReturn(collect([(object)['idGrupo' => 1]]));

        // Simulem que el grup té un tutor amb email
        $this->mockGrupo->shouldReceive('find')
            ->with(1)
            ->andReturn((object)[
                'idGrupo' => 1,  // ✅ Assegurar que el grup té `idGrupo`
                'Tutor' => (object)['email' => 'tutor@example.com']
            ]);

        AdviseTeacher::sendEmailTutor($elemento);

        // Comprovem que `SendEmail` s'ha afegit a la cua
        Queue::assertPushed(SendEmail::class);
    }
}
