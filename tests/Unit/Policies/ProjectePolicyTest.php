<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Grupo;
use Intranet\Entities\Projecte;
use Intranet\Policies\ProjectePolicy;
use Mockery;
use Tests\TestCase;

/**
 * Tests unitaris de la policy de projectes.
 */
class ProjectePolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_send_i_actes_permeten_tutor_amb_grup(): void
    {
        $this->mockTutorGroupService('TUT001', 'G1');
        $policy = new ProjectePolicy();
        $user = (object) ['dni' => 'TUT001', 'sustituye_a' => null];

        $this->assertTrue($policy->create($user));
        $this->assertTrue($policy->send($user));
        $this->assertTrue($policy->createActa($user));
        $this->assertTrue($policy->createDefenseActa($user));
    }

    public function test_create_i_accions_globals_deneguen_sense_grup_de_tutoria(): void
    {
        $this->mockTutorGroupService('TUT002', null);
        $policy = new ProjectePolicy();
        $user = (object) ['dni' => 'TUT002', 'sustituye_a' => null];

        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->send($user));
        $this->assertFalse($policy->createActa($user));
        $this->assertFalse($policy->createDefenseActa($user));
    }

    public function test_view_update_delete_i_check_requerixen_que_el_projecte_siga_del_grup_tutoritzat(): void
    {
        $this->mockTutorGroupService('TUT003', 'G1');
        $policy = new ProjectePolicy();

        $user = (object) ['dni' => 'TUT003', 'sustituye_a' => null];
        $projecteDelGrup = new Projecte();
        $projecteDelGrup->grup = 'G1';

        $projecteAltreGrup = new Projecte();
        $projecteAltreGrup->grup = 'G2';

        $this->assertTrue($policy->view($user, $projecteDelGrup));
        $this->assertTrue($policy->update($user, $projecteDelGrup));
        $this->assertTrue($policy->delete($user, $projecteDelGrup));
        $this->assertTrue($policy->check($user, $projecteDelGrup));

        $this->assertFalse($policy->view($user, $projecteAltreGrup));
        $this->assertFalse($policy->update($user, $projecteAltreGrup));
        $this->assertFalse($policy->delete($user, $projecteAltreGrup));
        $this->assertFalse($policy->check($user, $projecteAltreGrup));
    }

    /**
     * Configura el mock del servei de grups per retornar (o no) grup de tutoria.
     */
    private function mockTutorGroupService(string $dni, ?string $groupCode): void
    {
        $group = null;
        if ($groupCode !== null) {
            $group = new Grupo();
            $group->codigo = $groupCode;
        }

        $grupoService = Mockery::mock(GrupoService::class);
        $grupoService
            ->shouldReceive('byTutorOrSubstitute')
            ->with($dni, null)
            ->andReturn($group);

        $this->app->instance(GrupoService::class, $grupoService);
    }
}
