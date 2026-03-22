<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\TipoIncidencia;
use Intranet\Policies\TipoIncidenciaPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de tipus d'incidència.
 */
class TipoIncidenciaPolicyTest extends TestCase
{
    public function test_mutacions_nom_per_administrador(): void
    {
        $policy = new TipoIncidenciaPolicy();
        $tipoIncidencia = new TipoIncidencia();

        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $tipoIncidencia));
        $this->assertTrue($policy->delete($admin, $tipoIncidencia));

        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $tipoIncidencia));
        $this->assertFalse($policy->delete($teacher, $tipoIncidencia));
    }
}
