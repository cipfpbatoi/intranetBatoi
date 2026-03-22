<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use Intranet\Entities\Incidencia;
use Intranet\Policies\IncidenciaPolicy;
use Tests\TestCase;

class IncidenciaPolicyTest extends TestCase
{
    public function test_view_any_requerix_usuari_amb_dni(): void
    {
        $policy = new IncidenciaPolicy();

        $this->assertTrue($policy->viewAny((object) ['dni' => 'PRF001']));
        $this->assertFalse($policy->viewAny((object) []));
        $this->assertFalse($policy->viewAny(null));
    }

    public function test_create_permet_professor_autenticat(): void
    {
        $policy = new IncidenciaPolicy();

        $this->assertTrue($policy->create((object) ['dni' => 'PRF001']));
    }

    public function test_create_denega_usuari_invalid(): void
    {
        $policy = new IncidenciaPolicy();

        $this->assertFalse($policy->create((object) []));
        $this->assertFalse($policy->create(null));
    }

    public function test_update_view_delete_apliquen_regla_de_creador_o_responsable(): void
    {
        $policy = new IncidenciaPolicy();
        $incidencia = new Incidencia();
        $incidencia->idProfesor = 'PRF001';
        $incidencia->responsable = 'PRF002';

        $this->assertTrue($policy->update((object) ['dni' => 'PRF001'], $incidencia));
        $this->assertTrue($policy->view((object) ['dni' => 'PRF002'], $incidencia));
        $this->assertTrue($policy->delete((object) ['dni' => 'PRF002'], $incidencia));
        $this->assertFalse($policy->update((object) ['dni' => 'PRF999'], $incidencia));
    }
}
