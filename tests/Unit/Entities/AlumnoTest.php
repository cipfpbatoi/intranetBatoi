<?php
namespace Tests\Unit\Entities;

use Tests\TestCase;
use Intranet\Entities\Alumno;
use Mockery;
use Illuminate\Support\Carbon;

class AlumnoTest extends TestCase
{
    public function testGetFullNameAttribute()
    {
        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->nombre = 'JOAN';
        $alumno->apellido1 = 'GARCIA';
        $alumno->apellido2 = 'LOPEZ';

        $this->assertEquals('Joan Garcia Lopez', $alumno->full_name);
    }

    public function testGetShortNameAttribute()
    {
        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->nombre = 'Maria';
        $alumno->apellido1 = 'Perez';

        $this->assertEquals('Maria Perez', $alumno->short_name);
    }

    public function testGetFechaNacAttribute()
    {
        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->fecha_nac = '2005-03-01';

        $this->assertEquals('01-03-2005', $alumno->fecha_nac);
    }

    public function testGetEdatAttribute()
    {
        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->fecha_nac = (new Carbon())->subYears(20)->format('Y-m-d'); // Nascut fa 20 anys

        $this->assertEquals(20, $alumno->edat);
    }

    public function testGetEsMenorAttribute()
    {
        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->fecha_nac = (new Carbon())->subYears(17)->format('d-m-Y'); // Té 17 anys
        $alumno->fecha_nac2 = (new Carbon())->subYears(17)->format('Y-m-d');

        $this->assertTrue($alumno->esMenor);
        $this->assertTrue($alumno->esMenor);
    }

    public function testGetIdGrupoAttribute()
    {
        $grupoMock = Mockery::mock();
        $grupoMock->codigo = 'GRP2024';

        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->shouldReceive('getRelationValue')->with('Grupo')->andReturn(collect([$grupoMock]));

        $this->assertEquals('GRP2024', $alumno->id_grupo);
    }

    public function testGetHorasFctAttribute()
    {
        $fctMock1 = Mockery::mock();
        $fctMock1->horas = 100;

        $fctMock2 = Mockery::mock();
        $fctMock2->horas = 50;

        $alumno = Mockery::mock(Alumno::class)->makePartial();
        $alumno->shouldReceive('getRelationValue')->with('AlumnoFct')->andReturn(collect([$fctMock1, $fctMock2]));

        $this->assertEquals(150, $alumno->horas_fct);
    }
}
