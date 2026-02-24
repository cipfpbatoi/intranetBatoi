<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Signatura;
use Intranet\Policies\SignaturaPolicy;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de signatures.
 */
class SignaturaPolicyTest extends TestCase
{
    public function test_manage_i_create_permeten_professor_amb_dni(): void
    {
        $policy = new SignaturaPolicy();

        $this->assertTrue($policy->manage((object) ['dni' => 'PRF001']));
        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->manage((object) ['rol' => (int) config('roles.rol.profesor')]));
    }

    public function test_view_update_delete_apliquen_propietari_o_rol_elevat(): void
    {
        $policy = new SignaturaPolicy();
        $signatura = new Signatura();
        $signatura->idProfesor = 'PRF001';

        $this->assertTrue($policy->view((object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')], $signatura));
        $this->assertTrue($policy->update((object) ['dni' => 'DIR001', 'rol' => (int) config('roles.rol.direccion')], $signatura));
        $this->assertTrue($policy->delete((object) ['dni' => 'ADM001', 'rol' => (int) config('roles.rol.administrador')], $signatura));
        $this->assertFalse($policy->update((object) ['dni' => 'PRF999', 'rol' => (int) config('roles.rol.profesor')], $signatura));
    }
}
