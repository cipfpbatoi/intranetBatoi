<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Actividad;
use Intranet\Policies\ActividadPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy d'activitats.
 */
class ActividadPolicyTest extends TestCase
{
    public function test_create_permet_usuari_amb_dni_i_denega_invalid(): void
    {
        $policy = new ActividadPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_view_requerix_identitat_pero_la_gestio_requerix_coordinador_o_rol_elevat(): void
    {
        $policy = new ActividadPolicy();
        $actividad = new class extends Actividad {
            public function Creador()
            {
                return 'COO001';
            }
        };

        $coordinador = (object) ['dni' => 'COO001', 'rol' => (int) config('roles.rol.profesor')];
        $direccio = (object) ['dni' => 'DIR001', 'rol' => (int) config('roles.rol.direccion')];
        $admin = (object) ['dni' => 'ADM001', 'rol' => (int) config('roles.rol.administrador')];
        $altreProfessor = (object) ['dni' => 'PRF002', 'rol' => (int) config('roles.rol.profesor')];
        $invalid = (object) [];

        $this->assertTrue($policy->view($coordinador, $actividad));
        $this->assertTrue($policy->view($altreProfessor, $actividad));

        $this->assertTrue($policy->update($coordinador, $actividad));
        $this->assertTrue($policy->manageParticipants($coordinador, $actividad));
        $this->assertTrue($policy->notify($coordinador, $actividad));

        $this->assertTrue($policy->update($direccio, $actividad));
        $this->assertTrue($policy->manageParticipants($direccio, $actividad));
        $this->assertTrue($policy->notify($admin, $actividad));

        $this->assertFalse($policy->update($altreProfessor, $actividad));
        $this->assertFalse($policy->manageParticipants($altreProfessor, $actividad));
        $this->assertFalse($policy->notify($altreProfessor, $actividad));

        $this->assertFalse($policy->view($invalid, $actividad));
        $this->assertFalse($policy->update($invalid, $actividad));
        $this->assertFalse($policy->manageParticipants($invalid, $actividad));
        $this->assertFalse($policy->notify($invalid, $actividad));
    }
}
