<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Proves de contracte de noms de ruta serialitzables.
 */
class RouteNameContractTest extends TestCase
{
    public function test_no_hi_ha_noms_de_ruta_duplicats(): void
    {
        $routesByName = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if ($name === null || $name === '') {
                continue;
            }

            $routesByName[$name][] = implode('|', $route->methods()) . ' ' . $route->uri();
        }

        $duplicates = array_filter(
            $routesByName,
            static fn(array $routes): bool => count($routes) > 1
        );

        $this->assertSame([], $duplicates);
    }

    public function test_rutes_legacy_d_activitat_continuen_resolent(): void
    {
        $this->assertSame('/actividad/create', route('actividad.store', absolute: false));
        $this->assertSame('/actividad/1/edit', route('actividad.update', ['actividad' => 1], false));
        $this->assertSame('/actividad/1/detalle', route('actividad.detalle', ['actividad' => 1], false));
        $this->assertSame('/actividad/1/gestor', route('actividad.gestor', ['actividad' => 1], false));
        $this->assertSame('/actividadorientacion/create', route('actividad.createOrientacion', absolute: false));
        $this->assertSame('/actividadorientacion/create', route('actividad.storeOrientacion', absolute: false));
    }
}
