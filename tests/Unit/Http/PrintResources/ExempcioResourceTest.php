<?php

namespace Tests\Unit\Http\PrintResources;

use Illuminate\Support\Carbon;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Http\PrintResources\ExempcioResource;
use Tests\TestCase;

/**
 * Verifica el contracte entre l'informe d'exempció d'FE i els seus camps PDF.
 */
class ExempcioResourceTest extends TestCase
{
    /**
     * Comprova que només s'emplenen els camps automàtics de la plantilla nova.
     */
    public function test_to_array_mapeja_els_camps_del_nou_informe(): void
    {
        Carbon::setTestNow('2026-11-06 10:00:00');
        config([
            'contacto.nombre' => 'CIP FP Batoi',
            'contacto.codi' => '03012165',
            'contacto.poblacion' => 'Alcoi',
        ]);

        $tutor = new Profesor();
        $tutor->forceFill([
            'dni' => '12345678A',
            'nombre' => 'MARIA',
            'apellido1' => 'PEREZ',
            'apellido2' => 'SOLER',
            'movil1' => '',
            'movil2' => '600000001',
            'email' => 'tutora@example.com',
        ]);
        $this->actingAs($tutor, 'profesor');

        $alumno = new Alumno();
        $alumno->forceFill([
            'nia' => '12345678',
            'nombre' => 'JOAN',
            'apellido1' => 'FERRER',
            'apellido2' => 'MIRA',
            'telef1' => '600000000',
            'telef2' => '',
            'email' => 'alumne@example.com',
        ]);

        $ciclo = new Ciclo(['vliteral' => 'Desenvolupament d’Aplicacions Web']);
        $grupo = new Grupo();
        $grupo->setRelation('Ciclo', $ciclo);

        $grupoService = $this->createMock(GrupoService::class);
        $grupoService->expects($this->once())
            ->method('largestByTutor')
            ->with('12345678A')
            ->willReturn($grupo);
        $this->app->instance(GrupoService::class, $grupoService);

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRelation('Alumno', $alumno);

        $fields = (new ExempcioResource($alumnoFct))->toArray();

        $this->assertSame([
            'Text1' => 'NIA: 12345678 - Joan Ferrer Mira - 600000000 - alumne@example.com',
            'Text2' => 'CIP FP Batoi 03012165',
            'Text3' => 'Desenvolupament d’Aplicacions Web',
            'Text4' => 'DNI: 12345678A - Maria Perez Soler - 600000001 - tutora@example.com',
            'Tutora del centre educatiu    Tutora del centro educativo' => 'Alcoi',
            'undefined' => '06',
            'de' => month(hoy()),
            'Text20' => '26',
        ], $fields);
        $this->assertArrayNotHasKey('Signatura  Firma', $fields);
        $this->assertArrayNotHasKey('Text15', $fields);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
