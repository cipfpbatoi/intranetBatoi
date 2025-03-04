<?php


namespace Tests\Unit\Entities;

use PHPUnit\Framework\TestCase;
use Mockery;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Alumno;
use Intranet\Entities\Fct;



class AlumnoFctTest extends TestCase
{

    /** @test */
    public function get_email_attribute_retorna_email_correcte()
    {
        $mockAlumno = Mockery::mock(Alumno::class)->makePartial();

        // Evitem error `offsetExists()`
        $mockAlumno->shouldReceive('offsetExists')->andReturn(true);

        // Simulem l'atribut email
        $mockAlumno->shouldReceive('getAttribute')
            ->andReturnUsing(fn($key) => match ($key) {
                'email' => 'test@example.com',
                default => null,
            });

        $alumnoFct = Mockery::mock(AlumnoFct::class)->makePartial();
        $alumnoFct->setRelation('Alumno', $mockAlumno);

        $this->assertEquals('test@example.com', $alumnoFct->email);
    }

    /** @test */

    public function get_nombre_attribute_retorna_nom_correcte()
    {
        $mockAlumno = Mockery::mock(Alumno::class)->makePartial();

        // Evitem error `offsetExists()`
        $mockAlumno->shouldReceive('offsetExists')->andReturn(true);

        // Simulem l'atribut ShortName
        $mockAlumno->shouldReceive('getAttribute')
            ->andReturnUsing(fn($key) => match ($key) {
                'ShortName' => 'Joan',
                default => null,
            });

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRelation('Alumno', $mockAlumno);

        $this->assertEquals('Joan', $alumnoFct->nombre);
    }

    /** @test */
    public function get_centro_attribute_trunca_el_text_correctament()
    {
        $mockFct = Mockery::mock(Fct::class)->makePartial();

        // Evitem error `offsetExists()`
        $mockFct->shouldReceive('offsetExists')->andReturn(true);

        // Simulem l'atribut Centro
        $mockFct->shouldReceive('getAttribute')
            ->with('Centro')
            ->andReturn('Institut de Tecnologia Avançada Barcelona');

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRelation('Fct', $mockFct);

        $this->assertEquals('Institut de Tecnologia Avança', $alumnoFct->centro);
    }

    /** @test */
    public function get_fin_practicas_attribute_retorna_setmanes_i_dies()
    {
        $alumnoFct = new AlumnoFct();
        $alumnoFct->horas_diarias = 5;
        $alumnoFct->horas = 100;
        $alumnoFct->realizadas = 50;

        $this->assertEquals('2 Setmanes - 0 Dia', $alumnoFct->finPracticas);
    }

    /** @test */
    public function get_qualificacio_attribute_retorna_text_correcte()
    {
        $alumnoFct = new AlumnoFct();

        $alumnoFct->calificacion = 0;
        $this->assertEquals('No Apte', $alumnoFct->qualificacio);

        $alumnoFct->calificacion = 1;
        $this->assertEquals('Apte', $alumnoFct->qualificacio);

        $alumnoFct->calificacion = 2;
        $this->assertEquals('Convalidat/Exempt', $alumnoFct->qualificacio);

        $alumnoFct->calificacion = null;
        $this->assertEquals('No Avaluat', $alumnoFct->qualificacio);
    }

    /** @test */
    public function get_projecte_attribute_retorna_correcte()
    {
        $alumnoFct = new AlumnoFct();

        $alumnoFct->calProyecto = 0;
        $this->assertEquals('No presenta', $alumnoFct->projecte);

        $alumnoFct->calProyecto = null;
        $this->assertEquals('No Avaluat', $alumnoFct->projecte);

        $alumnoFct->calProyecto = 9;
        $this->assertEquals(9, $alumnoFct->projecte);
    }


    /** @test */
    public function get_class_attribute_retorna_classe_correcta()
    {

        $alumnoFct = Mockery::mock(AlumnoFct::class)->makePartial();

        // Simulem valors diferents per `asociacion`
        $alumnoFct->shouldReceive('getAttribute')
            ->with('asociacion')
            ->andReturn(2);
        $this->assertEquals('bg-purple', $alumnoFct->class);

        $alumnoFct = Mockery::mock(AlumnoFct::class)->makePartial();
        $alumnoFct->shouldReceive('getAttribute')
            ->with('asociacion')
            ->andReturn(3);
        $this->assertEquals('bg-orange', $alumnoFct->class);


    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
