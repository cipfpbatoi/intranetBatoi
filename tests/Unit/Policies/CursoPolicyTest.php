<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Curso;
use Intranet\Policies\CursoPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de cursos.
 */
class CursoPolicyTest extends TestCase
{
    public function test_mutacions_per_usuari_amb_dni_i_denegacio_sense_identitat(): void
    {
        $policy = new CursoPolicy();
        $curso = new Curso();

        $professor = (object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($professor));
        $this->assertTrue($policy->update($professor, $curso));
        $this->assertTrue($policy->delete($professor, $curso));

        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
        $this->assertFalse($policy->update(null, $curso));
    }
}
