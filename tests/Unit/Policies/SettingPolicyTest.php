<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Setting;
use Intranet\Policies\SettingPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de settings.
 */
class SettingPolicyTest extends TestCase
{
    public function test_create_update_delete_permeten_només_administrador(): void
    {
        $policy = new SettingPolicy();
        $setting = new Setting();
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $profe = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $setting));
        $this->assertTrue($policy->delete($admin, $setting));

        $this->assertFalse($policy->create($profe));
        $this->assertFalse($policy->update($profe, $setting));
        $this->assertFalse($policy->delete($profe, $setting));
    }
}
