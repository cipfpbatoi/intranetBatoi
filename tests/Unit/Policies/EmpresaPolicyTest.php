<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Empresa;
use Intranet\Policies\EmpresaPolicy;
use Tests\TestCase;

class EmpresaPolicyTest extends TestCase
{
    public function test_create_permet_tutor_jefe_practiques_i_direccio(): void
    {
        $policy = new EmpresaPolicy();

        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.tutor')]));
        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.jefe_practicas')]));
        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.direccion')]));
    }

    public function test_create_denega_rol_no_permes_o_usuari_invalid(): void
    {
        $policy = new EmpresaPolicy();

        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.alumno')]));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_aplica_la_mateixa_regla_que_create(): void
    {
        $policy = new EmpresaPolicy();
        $empresa = new Empresa();

        $this->assertTrue($policy->update((object) ['rol' => (int) config('roles.rol.tutor')], $empresa));
        $this->assertFalse($policy->update((object) ['rol' => (int) config('roles.rol.alumno')], $empresa));
    }
}
