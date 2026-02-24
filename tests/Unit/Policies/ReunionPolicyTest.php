<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Reunion;
use Intranet\Policies\ReunionPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de l'autorització de reunions.
 */
class ReunionPolicyTest extends TestCase
{
    public function test_create_permet_usuari_amb_dni_i_denega_invalid(): void
    {
        $policy = new ReunionPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_manage_i_notify_requerixen_ser_convocant(): void
    {
        $policy = new ReunionPolicy();
        $reunion = new Reunion();
        $reunion->idProfesor = 'PRF001';

        $owner = (object) ['dni' => 'PRF001'];
        $other = (object) ['dni' => 'PRF999'];

        $this->assertTrue($policy->update($owner, $reunion));
        $this->assertTrue($policy->manageParticipants($owner, $reunion));
        $this->assertTrue($policy->manageOrder($owner, $reunion));
        $this->assertTrue($policy->notify($owner, $reunion));

        $this->assertFalse($policy->update($other, $reunion));
        $this->assertFalse($policy->manageParticipants($other, $reunion));
        $this->assertFalse($policy->manageOrder($other, $reunion));
        $this->assertFalse($policy->notify($other, $reunion));
    }
}
