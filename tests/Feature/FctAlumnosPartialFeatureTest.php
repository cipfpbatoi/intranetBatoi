<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

/**
 * Regressió de la partial d'alumnat FCT quan falta el tutor assignat.
 */
class FctAlumnosPartialFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('sustituye_a')->nullable();
        });
    }

    public function test_partial_renders_when_alumno_fct_has_null_tutor(): void
    {
        $user = new Profesor();
        $user->dni = 'PRFTEST';

        $this->actingAs($user, 'profesor');

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
        $alumnoFct->idProfesor = 'ALTRE';

        $fct = new \stdClass();
        $fct->alFct = new Collection([$alumnoFct]);

        $html = view('fct.partials.alumnos', compact('fct'))->render();

        $this->assertStringContainsString('Alumne Prova', $html);
        $this->assertStringContainsString('Tutor del grup', $html);
        $this->assertStringNotContainsString('Fitxers Annexes', $html);
        $this->assertStringNotContainsString('Enllaçar fitxers', $html);
    }

    public function test_partial_shows_sao_annex_files_to_substitute_teacher(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'PTIT', 'sustituye_a' => null],
            ['dni' => 'PSUB', 'sustituye_a' => 'PTIT'],
        ]);

        $user = new Profesor();
        $user->dni = 'PSUB';

        $this->actingAs($user, 'profesor');

        $alumno = new \stdClass();
        $alumno->FullName = 'Alumne amb annexos';
        $alumno->telef1 = '600000001';
        $alumno->email = 'annexos@example.test';
        $alumno->Tutor = new Collection();

        $tutorFct = new \stdClass();
        $tutorFct->fullName = 'Tutor titular';

        $alumnoFct = new \stdClass();
        $alumnoFct->Alumno = $alumno;
        $alumnoFct->Tutor = $tutorFct;
        $alumnoFct->desde = '2026-03-01';
        $alumnoFct->hasta = '2026-04-01';
        $alumnoFct->horas = 380;
        $alumnoFct->idSao = 'SAO-1';
        $alumnoFct->saoAnnexes = 0;
        $alumnoFct->correoAlumno = 0;
        $alumnoFct->id = 2;
        $alumnoFct->idProfesor = 'PTIT';

        $fct = new \stdClass();
        $fct->alFct = new Collection([$alumnoFct]);

        $html = view('fct.partials.alumnos', compact('fct'))->render();

        $this->assertStringContainsString('SAO -', $html);
        $this->assertStringContainsString('Fitxers Annexes', $html);
        $this->assertStringContainsString('Enllaçar fitxers', $html);
    }
}
