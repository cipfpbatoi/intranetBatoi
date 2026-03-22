<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Colaboracion;
use Intranet\Policies\ColaboracionPolicy;
use Tests\TestCase;

class ColaboracionPolicyTest extends TestCase
{
    public function test_create_permet_tutor(): void
    {
        $policy = new ColaboracionPolicy();

        $this->assertTrue($policy->create((object) ['rol' => (int) config('roles.rol.tutor')]));
    }

    public function test_create_denega_no_tutor_o_usuari_invalid(): void
    {
        $policy = new ColaboracionPolicy();

        $this->assertFalse($policy->create((object) ['rol' => (int) config('roles.rol.alumno')]));
        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_aplica_mateixa_regla_que_create(): void
    {
        $policy = new ColaboracionPolicy();
        $colaboracion = new Colaboracion();

        $this->assertTrue($policy->update((object) ['rol' => (int) config('roles.rol.tutor')], $colaboracion));
        $this->assertFalse($policy->update((object) ['rol' => (int) config('roles.rol.profesor')], $colaboracion));
    }
}
