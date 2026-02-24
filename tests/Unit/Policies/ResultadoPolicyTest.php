<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Resultado;
use Intranet\Policies\ResultadoPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de resultats.
 */
class ResultadoPolicyTest extends TestCase
{
    public function test_create_permet_professor_amb_dni(): void
    {
        $policy = new ResultadoPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.profesor')]));
    }

    public function test_view_update_delete_apliquen_propietari_o_rol_elevat(): void
    {
        $policy = new ResultadoPolicy();
        $resultado = new Resultado();
        $resultado->idProfesor = 'PRF001';

        $this->assertTrue($policy->view((object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')], $resultado));
        $this->assertTrue($policy->update((object) ['dni' => 'DIR001', 'rol' => (int) config('roles.rol.direccion')], $resultado));
        $this->assertTrue($policy->delete((object) ['dni' => 'ADM001', 'rol' => (int) config('roles.rol.administrador')], $resultado));
        $this->assertFalse($policy->update((object) ['dni' => 'PRF999', 'rol' => (int) config('roles.rol.profesor')], $resultado));
    }
}
