<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Documento;

use Intranet\Application\Documento\DocumentoFormService;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Projecte;
use Tests\TestCase;

class DocumentoFormServiceTest extends TestCase
{
    public function test_project_defaults_reusen_dades_del_projecte_i_de_l_alumne(): void
    {
        $service = new DocumentoFormService();
        $projecte = new Projecte([
            'titol' => 'Projecte final',
            'descripcio' => 'Descripcio del projecte',
        ]);
        $alumno = new Alumno([
            'nombre' => 'Pau',
            'apellido1' => 'Garcia',
            'apellido2' => 'Marti',
        ]);
        $alumno->setRelation('Projecte', $projecte);

        $fct = new AlumnoFct();
        $fct->setRelation('Alumno', $alumno);

        $defaults = $service->projectDefaults($fct, 'DAM', 'Tutor Proves');

        $this->assertSame(Curso(), $defaults['curso']);
        $this->assertSame('Pau Garcia Marti', $defaults['propietario']);
        $this->assertSame('Tutor Proves', $defaults['supervisor']);
        $this->assertTrue($defaults['activo']);
        $this->assertSame('Proyecto', $defaults['tipoDocumento']);
        $this->assertSame('DAM', $defaults['ciclo']);
        $this->assertSame('Projecte final', $defaults['descripcion']);
        $this->assertSame('Descripcio del projecte', $defaults['detalle']);
    }
}
