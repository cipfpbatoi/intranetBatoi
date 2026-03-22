<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Poll\Option;
use Intranet\Policies\OptionPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy d'opcions de poll.
 */
class OptionPolicyTest extends TestCase
{
    public function test_create_i_delete_nom_per_rol_qualitat(): void
    {
        $policy = new OptionPolicy();
        $option = new Option();

        $qualitat = (object) ['rol' => (int) config('roles.rol.qualitat')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($qualitat));
        $this->assertTrue($policy->delete($qualitat, $option));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->delete($teacher, $option));
    }
}
