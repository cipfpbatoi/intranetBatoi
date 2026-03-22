<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Policies\ModuloCicloPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de mòdul-cicle.
 */
class ModuloCicloPolicyTest extends TestCase
{
    public function test_mutacions_nom_per_administrador(): void
    {
        $policy = new ModuloCicloPolicy();
        $moduloCiclo = new Modulo_ciclo();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $moduloCiclo));
        $this->assertTrue($policy->delete($admin, $moduloCiclo));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $moduloCiclo));
        $this->assertFalse($policy->delete($teacher, $moduloCiclo));
    }
}
