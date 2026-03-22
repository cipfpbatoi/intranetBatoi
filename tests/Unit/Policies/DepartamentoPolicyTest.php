<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Departamento;
use Intranet\Policies\DepartamentoPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de departaments.
 */
class DepartamentoPolicyTest extends TestCase
{
    public function test_mutacions_nom_per_administrador(): void
    {
        $policy = new DepartamentoPolicy();
        $departamento = new Departamento();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $departamento));
        $this->assertTrue($policy->delete($admin, $departamento));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $departamento));
        $this->assertFalse($policy->delete($teacher, $departamento));
    }
}
