<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Support\Collection;
use Intranet\Entities\Instructor;
use Tests\TestCase;

class InstructorTest extends TestCase
{
    public function test_nfcts_usa_relacio_carregada_quan_existeix(): void
    {
        $instructor = new Instructor();
        $instructor->setRelation('Fcts', new Collection([new \stdClass(), new \stdClass()]));

        $this->assertSame(2, $instructor->nfcts);
    }

    public function test_nombre_formateja_correctament(): void
    {
        $instructor = new Instructor();
        $instructor->name = 'JOAN';
        $instructor->surnames = 'PEREZ GARCIA';

        $this->assertSame('Joan Perez Garcia', $instructor->nombre);
    }
}
