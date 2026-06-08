<?php

namespace Tests\Unit\Entities;

use Intranet\Entities\FctConvalidacion;
use Tests\TestCase;

/**
 * Proves del model de FCT fictícia per convalidacions i renúncies.
 */
class FctConvalidacionTest extends TestCase
{
    /**
     * Verifica les qualificacions disponibles per a crear FCT fictícies.
     */
    public function test_calificacion_options_inclou_convalidacio_i_renuncia(): void
    {
        $fct = new FctConvalidacion();

        $this->assertSame(
            [
                2 => 'Convalidat/Exempt',
                5 => 'Renúncia / No realitzada',
            ],
            $fct->getCalificacionOptions()
        );
    }
}
