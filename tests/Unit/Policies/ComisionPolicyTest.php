<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Comision;
use Intranet\Policies\ComisionPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de comissions.
 */
class ComisionPolicyTest extends TestCase
{
    public function test_create_permet_usuari_amb_dni_i_denega_invalid(): void
    {
        $policy = new ComisionPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_view_i_managefct_només_permeten_propietari(): void
    {
        $policy = new ComisionPolicy();
        $comision = new Comision();
        $comision->idProfesor = 'PRF001';
        $owner = (object) ['dni' => 'PRF001'];
        $other = (object) ['dni' => 'PRF002'];
        $invalid = (object) [];

        $this->assertTrue($policy->view($owner, $comision));
        $this->assertTrue($policy->manageFct($owner, $comision));
        $this->assertTrue($policy->update($owner, $comision));

        $this->assertFalse($policy->view($other, $comision));
        $this->assertFalse($policy->manageFct($other, $comision));
        $this->assertFalse($policy->view($invalid, $comision));
        $this->assertFalse($policy->manageFct($invalid, $comision));
    }

    public function test_update_continua_requerint_identitat(): void
    {
        $policy = new ComisionPolicy();
        $comision = new Comision();

        $this->assertTrue($policy->update((object) ['dni' => 'PRF099'], $comision));
        $this->assertFalse($policy->update((object) [], $comision));
        $this->assertFalse($policy->update(null, $comision));
    }
}
