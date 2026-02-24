<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\TipoActividad;
use Intranet\Policies\TipoActividadPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de tipus d'activitat.
 */
class TipoActividadPolicyTest extends TestCase
{
    public function test_create_permet_direccio_admin_i_cap_departament(): void
    {
        $policy = new TipoActividadPolicy();

        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.direccion')]));
        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.administrador')]));
        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.jefe_dpto')]));
        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }

    public function test_update_delete_apliquen_departament_per_a_cap_departament(): void
    {
        $policy = new TipoActividadPolicy();
        $tipo = new TipoActividad();
        $tipo->departamento_id = '12';

        $headSame = (object) ['rol' => (int) config('roles.rol.jefe_dpto'), 'departamento' => '12'];
        $headOther = (object) ['rol' => (int) config('roles.rol.jefe_dpto'), 'departamento' => '99'];

        $this->assertTrue($policy->update($headSame, $tipo));
        $this->assertFalse($policy->update($headOther, $tipo));
        $this->assertTrue($policy->delete((object) ['rol' => (int) config('roles.rol.direccion')], $tipo));
    }
}
