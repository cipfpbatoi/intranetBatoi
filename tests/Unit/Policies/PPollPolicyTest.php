<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Poll\PPoll;
use Intranet\Policies\PPollPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de plantilles de polls.
 */
class PPollPolicyTest extends TestCase
{
    public function test_operacions_nom_per_rol_qualitat(): void
    {
        $policy = new PPollPolicy();
        $ppoll = new PPoll();

        $qualitat = (object) ['rol' => (int) config('roles.rol.qualitat')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->view($qualitat, $ppoll));
        $this->assertTrue($policy->create($qualitat));
        $this->assertTrue($policy->update($qualitat, $ppoll));
        $this->assertTrue($policy->delete($qualitat, $ppoll));

        $this->assertFalse($policy->view($teacher, $ppoll));
        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $ppoll));
        $this->assertFalse($policy->delete($teacher, $ppoll));
    }
}
