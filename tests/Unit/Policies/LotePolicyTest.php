<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Lote;
use Intranet\Policies\LotePolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de lots.
 */
class LotePolicyTest extends TestCase
{
    public function test_mutacions_nom_per_direccio_o_admin(): void
    {
        $policy = new LotePolicy();
        $lote = new Lote();

        $direction = (object) ['rol' => (int) config('roles.rol.direccion')];
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($direction));
        $this->assertTrue($policy->update($admin, $lote));
        $this->assertTrue($policy->delete($direction, $lote));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $lote));
        $this->assertFalse($policy->delete($teacher, $lote));
    }
}
