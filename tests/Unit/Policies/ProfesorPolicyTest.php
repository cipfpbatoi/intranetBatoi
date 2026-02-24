<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Profesor;
use Intranet\Policies\ProfesorPolicy;
use Tests\TestCase;

class ProfesorPolicyTest extends TestCase
{
    public function test_update_permet_direccio_o_admin(): void
    {
        $policy = new ProfesorPolicy();
        $profesor = new Profesor();

        $this->assertTrue($policy->update(
            (object) ['dni' => 'DIR001', 'rol' => (int) config('roles.rol.direccion')],
            $profesor
        ));
        $this->assertTrue($policy->update(
            (object) ['dni' => 'ADM001', 'rol' => (int) config('roles.rol.administrador')],
            $profesor
        ));
        $this->assertFalse($policy->update(
            (object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.profesor')],
            $profesor
        ));
    }

    public function test_manage_quality_final_permet_jefe_practiques(): void
    {
        $policy = new ProfesorPolicy();
        $profesor = new Profesor();

        $this->assertTrue($policy->manageQualityFinal(
            (object) ['dni' => 'PRF001', 'rol' => (int) config('roles.rol.jefe_practicas')],
            $profesor
        ));
    }

    public function test_manage_quality_final_denega_si_no_te_rol_o_usuari_invalid(): void
    {
        $policy = new ProfesorPolicy();
        $profesor = new Profesor();

        $this->assertFalse($policy->manageQualityFinal(
            (object) ['dni' => 'PRF002', 'rol' => (int) config('roles.rol.profesor')],
            $profesor
        ));
        $this->assertFalse($policy->manageQualityFinal((object) ['rol' => (int) config('roles.rol.jefe_practicas')], $profesor));
        $this->assertFalse($policy->manageQualityFinal(null, $profesor));
    }
}
