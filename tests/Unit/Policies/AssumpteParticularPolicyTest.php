<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\AssumpteParticular;
use Intranet\Policies\AssumpteParticularPolicy;
use Tests\TestCase;

/**
 * Proves de propietat i resolució de les peticions d'assumptes particulars.
 */
class AssumpteParticularPolicyTest extends TestCase
{
    public function test_el_professor_nomes_gestiona_una_peticio_propia_pendent(): void
    {
        $policy = new AssumpteParticularPolicy();
        $peticio = new AssumpteParticular([
            'idProfesor' => 'PROF001',
            'estat' => AssumpteParticular::ESTAT_PENDENT,
        ]);

        $this->assertTrue($policy->view((object) ['dni' => 'PROF001', 'rol' => 3], $peticio));
        $this->assertTrue($policy->update((object) ['dni' => 'PROF001', 'rol' => 3], $peticio));
        $this->assertFalse($policy->view((object) ['dni' => 'PROF002', 'rol' => 3], $peticio));
        $this->assertFalse($policy->update((object) ['dni' => 'PROF002', 'rol' => 3], $peticio));

        $peticio->estat = AssumpteParticular::ESTAT_AUTORITZADA;
        $this->assertFalse($policy->update((object) ['dni' => 'PROF001', 'rol' => 3], $peticio));
    }

    public function test_nomes_direccio_o_administracio_poden_resoldre(): void
    {
        $policy = new AssumpteParticularPolicy();
        $peticio = new AssumpteParticular(['estat' => AssumpteParticular::ESTAT_PENDENT]);

        $this->assertTrue($policy->resolve((object) ['dni' => 'DIR001', 'rol' => 6], $peticio));
        $this->assertTrue($policy->resolve((object) ['dni' => 'ADM001', 'rol' => 33], $peticio));
        $this->assertFalse($policy->resolve((object) ['dni' => 'PROF001', 'rol' => 3], $peticio));
    }

    public function test_nomes_la_directora_configurada_pot_autoritzar_amb_la_seua_rubrica(): void
    {
        config(['avisos.director' => 'DIR001']);
        $policy = new AssumpteParticularPolicy();
        $peticio = new AssumpteParticular(['estat' => AssumpteParticular::ESTAT_PENDENT]);

        $this->assertTrue($policy->approve((object) ['dni' => 'DIR001', 'rol' => 6], $peticio));
        $this->assertFalse($policy->approve((object) ['dni' => 'DIR002', 'rol' => 6], $peticio));
        $this->assertFalse($policy->approve((object) ['dni' => 'ADM001', 'rol' => 33], $peticio));
    }
}
