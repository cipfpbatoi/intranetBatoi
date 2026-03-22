<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\GrupoTrabajo;
use Intranet\Policies\GrupoTrabajoPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de grups de treball.
 */
class GrupoTrabajoPolicyTest extends TestCase
{
    public function test_create_permet_professor_amb_dni(): void
    {
        $policy = new GrupoTrabajoPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }

    public function test_update_delete_manage_members_requerixen_ser_propietari(): void
    {
        $policy = new GrupoTrabajoPolicy();
        $grupo = $this->getMockBuilder(GrupoTrabajo::class)
            ->onlyMethods(['Creador'])
            ->getMock();
        $grupo->method('Creador')->willReturn('PRF001');

        $owner = (object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')];
        $other = (object) ['dni' => 'PRF999', 'rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->update($owner, $grupo));
        $this->assertTrue($policy->delete($owner, $grupo));
        $this->assertTrue($policy->manageMembers($owner, $grupo));
        $this->assertFalse($policy->manageMembers($other, $grupo));
    }
}
