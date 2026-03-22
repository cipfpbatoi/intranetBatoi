<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\IpGuardia;
use Intranet\Policies\IpGuardiaPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy d'IPs de guàrdia.
 */
class IpGuardiaPolicyTest extends TestCase
{
    public function test_mutacions_nom_per_administrador(): void
    {
        $policy = new IpGuardiaPolicy();
        $ip = new IpGuardia();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $ip));
        $this->assertTrue($policy->delete($admin, $ip));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $ip));
        $this->assertFalse($policy->delete($teacher, $ip));
    }
}
