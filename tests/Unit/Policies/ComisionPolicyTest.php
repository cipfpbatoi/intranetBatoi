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

    public function test_view_update_managefct_requerixen_identitat(): void
    {
        $policy = new ComisionPolicy();
        $comision = new Comision();
        $valid = (object) ['dni' => 'PRF002'];
        $invalid = (object) [];

        $this->assertTrue($policy->view($valid, $comision));
        $this->assertTrue($policy->update($valid, $comision));
        $this->assertTrue($policy->manageFct($valid, $comision));

        $this->assertFalse($policy->view($invalid, $comision));
        $this->assertFalse($policy->update($invalid, $comision));
        $this->assertFalse($policy->manageFct($invalid, $comision));
    }
}
