<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Fct;
use Intranet\Policies\FctPolicy;
use Tests\TestCase;

class FctPolicyTest extends TestCase
{
    public function test_view_any_aplica_regla_general_fct(): void
    {
        $policy = new FctPolicy();

        $this->assertTrue($policy->viewAny((object) ['rol' => (int) config('roles.rol.tutor')]));
        $this->assertTrue($policy->viewAny((object) ['rol' => (int) config('roles.rol.practicas')]));
        $this->assertFalse($policy->viewAny((object) ['rol' => (int) config('roles.rol.alumno')]));
        $this->assertFalse($policy->viewAny((object) []));
        $this->assertFalse($policy->viewAny(null));
    }

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

    public function test_abilities_de_flux_aval_apliquen_la_mateixa_regla_de_permisos(): void
    {
        $policy = new FctPolicy();
        $tutor = (object) ['rol' => (int) config('roles.rol.tutor')];
        $alumne = (object) ['rol' => (int) config('roles.rol.alumno')];

        $this->assertTrue($policy->manageAval($tutor));
        $this->assertTrue($policy->requestActa($tutor));
        $this->assertTrue($policy->sendA56($tutor));
        $this->assertTrue($policy->viewStats($tutor));

        $this->assertFalse($policy->manageAval($alumne));
        $this->assertFalse($policy->requestActa($alumne));
        $this->assertFalse($policy->sendA56($alumne));
        $this->assertFalse($policy->viewStats($alumne));
    }

    public function test_manage_pending_acta_permet_direccio_admin_i_jefe_practiques(): void
    {
        $policy = new FctPolicy();

        $direccio = (object) ['rol' => (int) config('roles.rol.direccion')];
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $jefePractiques = (object) ['rol' => (int) config('roles.rol.jefe_practicas')];
        $tutor = (object) ['rol' => (int) config('roles.rol.tutor')];

        $this->assertTrue($policy->managePendingActa($direccio));
        $this->assertTrue($policy->managePendingActa($admin));
        $this->assertTrue($policy->managePendingActa($jefePractiques));
        $this->assertFalse($policy->managePendingActa($tutor));
        $this->assertFalse($policy->managePendingActa((object) []));
        $this->assertFalse($policy->managePendingActa(null));
    }

    public function test_manage_dual_control_permet_jefe_practiques_direccio_i_admin(): void
    {
        $policy = new FctPolicy();

        $direccio = (object) ['rol' => (int) config('roles.rol.direccion')];
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $jefePractiques = (object) ['rol' => (int) config('roles.rol.jefe_practicas')];
        $tutor = (object) ['rol' => (int) config('roles.rol.tutor')];

        $this->assertTrue($policy->manageFctControl($direccio));
        $this->assertTrue($policy->manageFctControl($admin));
        $this->assertTrue($policy->manageFctControl($jefePractiques));
        $this->assertFalse($policy->manageFctControl($tutor));
        $this->assertFalse($policy->manageFctControl((object) []));
        $this->assertFalse($policy->manageFctControl(null));
    }
}
