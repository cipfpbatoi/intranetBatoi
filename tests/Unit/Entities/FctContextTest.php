<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Intranet\Entities\Centro;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FctContextTest extends TestCase
{
    public function test_related_context_methods_retornen_centre_empresa_i_cicle(): void
    {
        $empresa = new Empresa(['nombre' => 'Empresa test']);
        $centro = new Centro(['nombre' => 'Centre test', 'idEmpresa' => 9]);
        $centro->setRelation('Empresa', $empresa);

        $tutor = new Profesor(['dni' => 'P001', 'nombre' => 'Tutor']);
        $ciclo = new Ciclo(['ciclo' => 'SMX']);
        $ciclo->setRelation('TutoresFct', new Collection([$tutor]));

        $colaboracion = new Colaboracion(['contacto' => 'Contacte']);
        $colaboracion->setRelation('Centro', $centro);
        $colaboracion->setRelation('Ciclo', $ciclo);

        $fct = new Fct();
        $fct->setRelation('Colaboracion', $colaboracion);

        $this->assertSame($colaboracion, $fct->relatedColaboracion());
        $this->assertSame($centro, $fct->relatedCenter());
        $this->assertSame($empresa, $fct->relatedCompany());
        $this->assertSame($ciclo, $fct->relatedCycle());
        $this->assertCount(1, $fct->cycleTutors());
        $this->assertTrue($fct->hasOperationalContext());
    }

    public function test_related_context_methods_tolerate_missing_colaboracion(): void
    {
        $fct = new Fct();

        $this->assertNull($fct->relatedColaboracion());
        $this->assertNull($fct->relatedCenter());
        $this->assertNull($fct->relatedCompany());
        $this->assertNull($fct->relatedCycle());
        $this->assertTrue($fct->cycleTutors()->isEmpty());
        $this->assertFalse($fct->hasOperationalContext());
        $this->assertSame('Convalidada/Exent', $fct->centro);
        $this->assertNull($fct->ciclo);
    }
}
