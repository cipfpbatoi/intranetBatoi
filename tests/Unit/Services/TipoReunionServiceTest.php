<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Intranet\Services\Document\TipoReunionService;
use Tests\TestCase;

/**
 * Proves unitàries de la configuració dels tipus de reunió.
 */
class TipoReunionServiceTest extends TestCase
{
    /**
     * La defensa de projectes ha d'usar la mateixa plantilla en convocatòria i acta.
     */
    public function test_defensa_projecte_usa_acta_defensa_tambe_com_a_convocatoria(): void
    {
        $tipoReunion = TipoReunionService::find(12);

        $this->assertSame('actaDefensa', $tipoReunion->convocatoria);
        $this->assertSame($tipoReunion->acta, $tipoReunion->convocatoria);
    }
}
