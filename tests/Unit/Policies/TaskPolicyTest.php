<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Task;
use Intranet\Policies\TaskPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de tasques.
 */
class TaskPolicyTest extends TestCase
{
    public function test_create_update_permeten_només_administrador(): void
    {
        $policy = new TaskPolicy();
        $task = new Task();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $profe = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $task));
        $this->assertFalse($policy->create($profe));
        $this->assertFalse($policy->update($profe, $task));
    }

    public function test_check_requerix_usuari_amb_dni(): void
    {
        $policy = new TaskPolicy();
        $task = new Task();

        $this->assertTrue($policy->check((object) ['dni' => 'PRF001'], $task));
        $this->assertFalse($policy->check((object) [], $task));
        $this->assertFalse($policy->check(null, $task));
    }
}
