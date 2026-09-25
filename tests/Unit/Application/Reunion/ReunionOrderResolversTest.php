<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reunion;

use Intranet\Application\Reunion\OrderResolvers\LearningDifficultiesOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\LoeProjectStudentsOrderResolver;
use Intranet\Application\Reunion\OrderResolvers\ProjectDefenseStudentsOrderResolver;
use Intranet\Application\Reunion\ReunionOrderStudentQuery;
use Intranet\Application\Reunion\ReunionOrderTemplate;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;
use Mockery;
use Tests\TestCase;

/**
 * Proves dels resolutors explícits de contingut inicial de les actes.
 */
class ReunionOrderResolversTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resol_alumnat_amb_dificultats_del_convocant(): void
    {
        $students = Mockery::mock(ReunionOrderStudentQuery::class);
        $students->shouldReceive('learningDifficulties')
            ->once()
            ->with('P1')
            ->andReturn(collect(['Anna Test', 'Biel Test']));

        $drafts = (new LearningDifficultiesOrderResolver($students))->resolve(
            $this->reunion(),
            new ReunionOrderTemplate(
                OrdenReunion::CODE_NESE_FOLLOW_UP,
                'Alumnes amb dificultats acadèmiques i mesures a adoptar'
            )
        );

        $this->assertCount(1, $drafts);
        $this->assertSame(OrdenReunion::CODE_NESE_FOLLOW_UP, $drafts[0]->code);
        $this->assertSame('Anna Test, Biel Test', $drafts[0]->summary);
    }

    public function test_resol_un_punt_per_cada_alumne_loe(): void
    {
        $students = Mockery::mock(ReunionOrderStudentQuery::class);
        $students->shouldReceive('loeStudents')
            ->once()
            ->with('P1')
            ->andReturn(collect(['Anna Test', 'Biel Test']));

        $drafts = (new LoeProjectStudentsOrderResolver($students))->resolve(
            $this->reunion(),
            new ReunionOrderTemplate(
                OrdenReunion::CODE_PROJECT_PROPOSAL_STUDENT,
                null,
                'Títol i Tutor individual '
            )
        );

        $this->assertSame(['Anna Test', 'Biel Test'], array_column($drafts, 'description'));
        $this->assertSame('Títol i Tutor individual  1', $drafts[0]->summary);
        $this->assertSame('Títol i Tutor individual  2', $drafts[1]->summary);
    }

    public function test_resol_un_punt_per_cada_projecte_pendent_de_defensa(): void
    {
        $students = Mockery::mock(ReunionOrderStudentQuery::class);
        $students->shouldReceive('projectStudents')
            ->once()
            ->with('P1')
            ->andReturn(collect(['Carla Test']));

        $drafts = (new ProjectDefenseStudentsOrderResolver($students))->resolve(
            $this->reunion(),
            new ReunionOrderTemplate(
                OrdenReunion::CODE_PROJECT_DEFENSE_STUDENT,
                null,
                '(Projecte) Data i Hora '
            )
        );

        $this->assertCount(1, $drafts);
        $this->assertSame('Carla Test', $drafts[0]->description);
        $this->assertSame('(Projecte) Data i Hora  1', $drafts[0]->summary);
    }

    /**
     * Crea una reunió mínima amb el convocant explícit.
     */
    private function reunion(): Reunion
    {
        return new Reunion(['idProfesor' => 'P1']);
    }
}
