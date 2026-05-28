<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\PanelPresenciaController;
use Tests\TestCase;

/**
 * Proves unitàries del controlador del panell de presència.
 */
class PanelPresenciaControllerTest extends TestCase
{
    /**
     * Comprova que el paràmetre `dia` de la query s'utilitza en la llista.
     */
    public function test_usa_dia_de_query_si_no_hi_ha_parametre_de_ruta(): void
    {
        app()->instance('request', Request::create('/direccion/fichar/list', 'GET', [
            'dia' => '2026-05-01',
        ]));

        $controller = new PanelPresenciaController();

        $this->assertSame(
            '2026-05-01',
            $this->callProtectedMethod($controller, 'diaSeleccionat', [null])
        );
    }

    /**
     * Comprova que el paràmetre de ruta continua tenint prioritat sobre la query.
     */
    public function test_prioritza_dia_de_ruta_sobre_query(): void
    {
        app()->instance('request', Request::create('/direccion/fichar/list/2026-05-02', 'GET', [
            'dia' => '2026-05-01',
        ]));

        $controller = new PanelPresenciaController();

        $this->assertSame(
            '2026-05-02',
            $this->callProtectedMethod($controller, 'diaSeleccionat', ['2026-05-02'])
        );
    }
}
