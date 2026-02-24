<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Ciclo;
use Intranet\Policies\CicloPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de cicles.
 */
class CicloPolicyTest extends TestCase
{
    public function test_mutacions_nom_per_administrador(): void
    {
        $policy = new CicloPolicy();
        $ciclo = new Ciclo();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $ciclo));
        $this->assertTrue($policy->delete($admin, $ciclo));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $ciclo));
        $this->assertFalse($policy->delete($teacher, $ciclo));
    }
}
