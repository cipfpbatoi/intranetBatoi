<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Intranet\Entities\Profesor;
use Tests\TestCase;

/**
 * Regressió de la partial d'alumnat FCT quan falta el tutor assignat.
 */
class FctAlumnosPartialFeatureTest extends TestCase
{
    public function test_partial_renders_when_alumno_fct_has_null_tutor(): void
    {
        $user = new Profesor();
        $user->dni = 'PRFTEST';

        $this->actingAs($user);

        $tutorGrup = new \stdClass();
        $tutorGrup->FullName = 'Tutor del grup';

        $alumno = new \stdClass();
        $alumno->FullName = 'Alumne Prova';
        $alumno->telef1 = '600000000';
        $alumno->email = 'alumne@example.test';
        $alumno->Tutor = new Collection([$tutorGrup]);

        $alumnoFct = new \stdClass();
        $alumnoFct->Alumno = $alumno;
        $alumnoFct->Tutor = null;
        $alumnoFct->desde = '2026-01-01';
        $alumnoFct->hasta = '2026-02-01';
        $alumnoFct->horas = 380;
        $alumnoFct->idSao = null;
        $alumnoFct->saoAnnexes = 0;
        $alumnoFct->correoAlumno = 0;
        $alumnoFct->id = 1;

        $fct = new \stdClass();
        $fct->alFct = new Collection([$alumnoFct]);

        $html = view('fct.partials.alumnos', compact('fct'))->render();

        $this->assertStringContainsString('Alumne Prova', $html);
        $this->assertStringContainsString('Tutor del grup', $html);
    }
}
