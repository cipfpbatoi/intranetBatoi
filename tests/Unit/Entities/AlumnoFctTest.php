<?php


namespace Tests\Unit\Entities;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Mockery;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Alumno;
use Intranet\Entities\Fct;
use Intranet\Entities\Signatura;

class AlumnoFctTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function get_fin_practicas_attribute_retorna_setmanes_i_dies()
    {
        $alumnoFct = new AlumnoFct();
        $alumnoFct->horas_diarias = 5;
        $alumnoFct->horas = 100;
        $alumnoFct->realizadas = 50;

        $this->assertEquals('2 Setmanes - 0 Dia', $alumnoFct->finPracticas);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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
    #[Test]
    public function accessors_signatures_reutilitzen_relacio_carregada(): void
    {
        $sigA1 = new Signatura(['tipus' => 'A1', 'signed' => true]);
        $sigA2 = new Signatura(['tipus' => 'A2', 'signed' => true]);
        $sigA3 = new Signatura(['tipus' => 'A3', 'signed' => false]);

        $alumnoFct = new AlumnoFct();
        $alumnoFct->setRelation('Signatures', new Collection([$sigA1, $sigA2, $sigA3]));

        $this->assertTrue($alumnoFct->sign);
        $this->assertSame($sigA1, $alumnoFct->a1);
        $this->assertSame($sigA2, $alumnoFct->a2);
        $this->assertSame($sigA3, $alumnoFct->a3);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
