<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Garantix que el formulari modal d'alta d'espais apunta a l'acció `store`.
 */
class EspacioRoutesTest extends TestCase
{
    public function test_ruta_store_no_requerix_parametres_i_apunta_a_store(): void
    {
        $route = Route::getRoutes()->getByName('espacio.store');

        $this->assertNotNull($route);
        $this->assertSame('espacio/create', $route->uri());
        $this->assertContains('POST', $route->methods());
        $this->assertStringEndsWith('EspacioController@store', $route->getActionName());
        $this->assertStringEndsWith('/espacio/create', route('espacio.store'));
    }

    public function test_ruta_update_apunta_a_update(): void
    {
        $route = Route::getRoutes()->getByName('espacio.update');

        $this->assertNotNull($route);
        $this->assertSame('espacio/{espacio}/edit', $route->uri());
        $this->assertContains('PUT', $route->methods());
        $this->assertStringEndsWith('EspacioController@update', $route->getActionName());
    }
}
