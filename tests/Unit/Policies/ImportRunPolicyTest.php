<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\ImportRun;
use Intranet\Policies\ImportRunPolicy;
use Tests\TestCase;

class ImportRunPolicyTest extends TestCase
{
    public function test_manage_permet_administrador(): void
    {
        $policy = new ImportRunPolicy();

        $this->assertTrue($policy->manage((object) ['rol' => (int) config('roles.rol.administrador')]));
    }

    public function test_manage_denega_no_admin_o_usuari_invalid(): void
    {
        $policy = new ImportRunPolicy();

        $this->assertFalse($policy->manage((object) ['rol' => (int) config('roles.rol.profesor')]));
        $this->assertFalse($policy->manage((object) []));
        $this->assertFalse($policy->manage(null));
    }

    public function test_view_i_view_any_reutilitzen_manage(): void
    {
        $policy = new ImportRunPolicy();
        $importRun = new ImportRun();
        $admin = (object) ['rol' => (int) config('roles.rol.administrador')];
        $professor = (object) ['rol' => (int) config('roles.rol.profesor')];

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->view($admin, $importRun));
        $this->assertFalse($policy->viewAny($professor));
        $this->assertFalse($policy->view($professor, $importRun));
    }
}
