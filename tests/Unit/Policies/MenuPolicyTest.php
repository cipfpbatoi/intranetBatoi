<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Menu;
use Intranet\Policies\MenuPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de menú.
 */
class MenuPolicyTest extends TestCase
{
    public function test_mutacions_nom_per_administrador(): void
    {
        $policy = new MenuPolicy();
        $menu = new Menu();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $menu));
        $this->assertTrue($policy->delete($admin, $menu));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $menu));
        $this->assertFalse($policy->delete($teacher, $menu));
    }
}
