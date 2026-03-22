<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use Illuminate\Http\Request;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Intranet\Http\Resources\AlumnoFctControlResource;
use Tests\TestCase;

class AlumnoFctControlResourceTest extends TestCase
{
    public function test_to_array_uses_fct_context_helpers(): void
    {
        $centro = new Centro(['nombre' => 'Centre test']);
        $colaboracion = new Colaboracion();
        $colaboracion->setRelation('Centro', $centro);

        $fct = new Fct();
        $fct->setRelation('Colaboracion', $colaboracion);

        $alumno = new Alumno([
            'nombre' => 'Ada',
            'apellido1' => 'Lovelace',
            'apellido2' => '',
        ]);

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRawAttributes([
            'id' => 10,
            'a56' => 1,
            'pg0301' => 0,
            'desde' => '2026-03-01',
            'hasta' => '2026-03-15',
        ], true);
        $alumnoFct->setRelation('Fct', $fct);
        $alumnoFct->setRelation('Alumno', $alumno);

        $resource = new AlumnoFctControlResource($alumnoFct);

        $this->assertSame([
            'id' => 10,
            'centro' => 'Centre test',
            'nombre' => $alumno->fullName,
            'a56' => 1,
            'desde' => '01-03-2026',
            'hasta' => '15-03-2026',
            'pg0301' => 0,
        ], $resource->toArray(Request::create('/')));
    }

    public function test_to_array_tolerates_missing_fct_context(): void
    {
        $alumno = new Alumno([
            'nombre' => 'Grace',
            'apellido1' => 'Hopper',
            'apellido2' => '',
        ]);

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRawAttributes([
            'id' => 11,
            'a56' => 0,
            'pg0301' => 1,
            'desde' => '2026-04-01',
            'hasta' => '2026-04-30',
        ], true);
        $alumnoFct->setRelation('Alumno', $alumno);

        $resource = new AlumnoFctControlResource($alumnoFct);
        $data = $resource->toArray(Request::create('/'));

        $this->assertSame(11, $data['id']);
        $this->assertNull($data['centro']);
        $this->assertSame($alumno->fullName, $data['nombre']);
        $this->assertSame(0, $data['a56']);
        $this->assertSame('01-04-2026', $data['desde']);
        $this->assertSame('30-04-2026', $data['hasta']);
        $this->assertSame(1, $data['pg0301']);
    }
}
