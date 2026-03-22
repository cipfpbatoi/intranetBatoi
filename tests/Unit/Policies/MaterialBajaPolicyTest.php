<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\MaterialBaja;
use Intranet\Policies\MaterialBajaPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de baixes de material.
 */
class MaterialBajaPolicyTest extends TestCase
{
    public function test_operacions_critiques_nom_per_direccio_o_admin(): void
    {
        $policy = new MaterialBajaPolicy();
        $baja = new MaterialBaja();

        $direction = (object) ['rol' => (int) config('roles.rol.direccion')];
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->update($direction, $baja));
        $this->assertTrue($policy->delete($admin, $baja));
        $this->assertTrue($policy->recover($direction, $baja));

        $this->assertFalse($policy->update($teacher, $baja));
        $this->assertFalse($policy->delete($teacher, $baja));
        $this->assertFalse($policy->recover($teacher, $baja));
    }
}
