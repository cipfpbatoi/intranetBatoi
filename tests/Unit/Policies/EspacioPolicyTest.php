<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Espacio;
use Intranet\Policies\EspacioPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy d'espais.
 */
class EspacioPolicyTest extends TestCase
{
    public function test_crear_espais_nom_es_per_a_direccio(): void
    {
        $policy = new EspacioPolicy();
        $espacio = new Espacio();

        $direction = (object) ['rol' => (int) config('roles.rol.direccion')];
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $maintenance = (object) ['rol' => (int) config('roles.rol.mantenimiento')];
        $teacher = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->create($direction));
        $this->assertTrue($policy->update($admin, $espacio));
        $this->assertTrue($policy->delete($direction, $espacio));
        $this->assertTrue($policy->printBarcode($admin, $espacio));

        $this->assertFalse($policy->create($admin));
        $this->assertFalse($policy->create($maintenance));
        $this->assertFalse($policy->create($teacher));
        $this->assertFalse($policy->update($teacher, $espacio));
        $this->assertFalse($policy->delete($teacher, $espacio));
        $this->assertFalse($policy->printBarcode($teacher, $espacio));
    }
}
