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

    public function test_view_update_manage_i_notify_requerixen_identitat(): void
    {
        $policy = new ActividadPolicy();
        $actividad = new Actividad();
        $valid = (object) ['dni' => 'PRF002'];
        $invalid = (object) [];

        $this->assertTrue($policy->view($valid, $actividad));
        $this->assertTrue($policy->update($valid, $actividad));
        $this->assertTrue($policy->manageParticipants($valid, $actividad));
        $this->assertTrue($policy->notify($valid, $actividad));

        $this->assertFalse($policy->view($invalid, $actividad));
        $this->assertFalse($policy->update($invalid, $actividad));
        $this->assertFalse($policy->manageParticipants($invalid, $actividad));
        $this->assertFalse($policy->notify($invalid, $actividad));
    }
}
