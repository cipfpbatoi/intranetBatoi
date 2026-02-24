<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Solicitud;
use Intranet\Policies\SolicitudPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de sol·licituds.
 */
class SolicitudPolicyTest extends TestCase
{
    public function test_create_permet_professor_amb_dni(): void
    {
        $policy = new SolicitudPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }

    public function test_view_update_delete_apliquen_propietari_o_rol_elevat(): void
    {
        $policy = new SolicitudPolicy();
        $solicitud = new Solicitud();
        $solicitud->idProfesor = 'PRF001';
        $solicitud->idOrientador = 'ORI001';

        $this->assertTrue($policy->view((object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')], $solicitud));
        $this->assertTrue($policy->update((object) ['dni' => 'DIR001', 'rol' => (int) config('roles.rol.direccion')], $solicitud));
        $this->assertTrue($policy->update((object) ['dni' => 'ORI001', 'rol' => (int) config('roles.rol.orientador')], $solicitud));
        $this->assertTrue($policy->delete((object) ['dni' => 'ADM001', 'rol' => (int) config('roles.rol.administrador')], $solicitud));
        $this->assertFalse($policy->update((object) ['dni' => 'PRF999', 'rol' => (int) config('roles.rol.profesor')], $solicitud));
    }
}
