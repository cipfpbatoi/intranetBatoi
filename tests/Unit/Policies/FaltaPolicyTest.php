<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Falta;
use Intranet\Policies\FaltaPolicy;
use Tests\TestCase;

/**
 * Proves unitàries de la policy de faltes.
 */
class FaltaPolicyTest extends TestCase
{
    public function test_create_permet_professor_amb_dni(): void
    {
        $policy = new FaltaPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
    }

    public function test_update_permet_propietari_direccio_i_admin_i_denega_altres(): void
    {
        $policy = new FaltaPolicy();
        $falta = new Falta();
        $falta->idProfesor = 'PRF001';

        $this->assertTrue($policy->update((object) ['dni' => 'PRF001', 'rol' => config('roles.rol.profesor')], $falta));
        $this->assertTrue($policy->update((object) ['dni' => 'DIR001', 'rol' => config('roles.rol.direccion')], $falta));
        $this->assertTrue($policy->update((object) ['dni' => 'ADM001', 'rol' => config('roles.rol.administrador')], $falta));
        $this->assertFalse($policy->update((object) ['dni' => 'PRF999', 'rol' => config('roles.rol.profesor')], $falta));
    }

    public function test_delete_reutilitza_la_regla_d_ownership(): void
    {
        $policy = new FaltaPolicy();
        $falta = new Falta();
        $falta->idProfesor = 'PRF001';

        $this->assertTrue($policy->delete((object) ['dni' => 'PRF001', 'rol' => config('roles.rol.profesor')], $falta));
        $this->assertFalse($policy->delete((object) ['dni' => 'PRF002', 'rol' => config('roles.rol.profesor')], $falta));
    }
}
