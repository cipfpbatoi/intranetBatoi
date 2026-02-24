<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Fct;
use Intranet\Policies\FctPolicy;
use Tests\TestCase;

class FctPolicyTest extends TestCase
{
    public function test_create_permet_tutor_practiques_i_jefe_practiques(): void
    {
        $policy = new FctPolicy();

        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.tutor')]));
        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.practicas')]));
        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.jefe_practicas')]));
    }

    public function test_create_denega_rol_no_permes_o_usuari_invalid(): void
    {
        $policy = new FctPolicy();

        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.alumno')]));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_aplica_la_mateixa_regla_que_create(): void
    {
        $policy = new FctPolicy();
        $fct = new Fct();

        $this->assertTrue($policy->update((object) ['rol' => (int) config('roles.rol.practicas')], $fct));
        $this->assertFalse($policy->update((object) ['rol' => (int) config('roles.rol.alumno')], $fct));
    }

    public function test_delete_aplica_la_mateixa_regla_que_create(): void
    {
        $policy = new FctPolicy();
        $fct = new Fct();

        $this->assertTrue($policy->delete((object) ['rol' => (int) config('roles.rol.jefe_practicas')], $fct));
        $this->assertFalse($policy->delete((object) ['rol' => (int) config('roles.rol.alumno')], $fct));
    }
}
