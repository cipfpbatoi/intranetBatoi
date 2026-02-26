<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Expediente;
use Intranet\Policies\ExpedientePolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy d'expedients.
 */
class ExpedientePolicyTest extends TestCase
{
    public function test_create_permet_professor_amb_dni(): void
    {
        $policy = new ExpedientePolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }

    public function test_view_update_delete_apliquen_propietari_o_rol_elevat(): void
    {
        $policy = new ExpedientePolicy();
        $expediente = new Expediente();
        $expediente->idProfesor = 'PRF001';

        $this->assertTrue($policy->view((object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')], $expediente));
        $this->assertTrue($policy->update((object) ['dni' => 'DIR001', 'rol' => (int) config('roles.rol.direccion')], $expediente));
        $this->assertTrue($policy->delete((object) ['dni' => 'ADM001', 'rol' => (int) config('roles.rol.administrador')], $expediente));
        $this->assertFalse($policy->delete((object) ['dni' => 'PRF999', 'rol' => (int) config('roles.rol.profesor')], $expediente));
    }
}
