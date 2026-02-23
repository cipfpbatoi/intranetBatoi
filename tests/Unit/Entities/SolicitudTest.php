<?php

namespace Tests\Unit\Entities;

use Intranet\Entities\Alumno;
use Intranet\Entities\Solicitud;
use Intranet\Http\Resources\SolicitudResource;
use Tests\TestCase;

class SolicitudTest extends TestCase
{
    public function test_quien_accessor_usa_nom_alum_correcte(): void
    {
        $solicitud = new Solicitud([
            'text1' => 'Motiu de prova',
            'fecha' => '2026-02-22',
        ]);
        $solicitud->setRelation('Alumno', new Alumno([
            'nia' => 'A100',
            'nombre' => 'Marta',
            'apellido1' => 'Vila',
            'apellido2' => 'Serra',
        ]));

        $this->assertSame('Marta Vila Serra', $solicitud->nomAlum);
        $this->assertSame('Marta Vila Serra', $solicitud->quien);
    }

    public function test_dates_i_nom_alum_son_null_safe(): void
    {
        $solicitud = new Solicitud([
            'fecha' => null,
            'fechasolucion' => null,
        ]);
        $solicitud->setRelation('Alumno', null);

        $this->assertSame('', $solicitud->fecha);
        $this->assertSame('', $solicitud->fechaSolucion);
        $this->assertSame('', $solicitud->nomAlum);
    }

    public function test_solicitud_resource_data_solucio_i_campos_null_safe(): void
    {
        $solicitud = new Solicitud([
            'text1' => 'Text principal',
            'text2' => null,
            'text3' => null,
            'solucion' => 'Resolució',
            'estado' => 0,
            'fecha' => null,
            'fechasolucion' => null,
        ]);
        $solicitud->setRelation('Alumno', null);
        $solicitud->setRelation('Profesor', null);
        $solicitud->setRelation('Orientador', null);

        $data = (new SolicitudResource($solicitud))->toArray(request());

        $this->assertSame('', $data['Alumne']);
        $this->assertSame('', $data['Email']);
        $this->assertSame('', $data['Data Solució']);
    }
}
