<?php

namespace Tests\Browser\Actividad;

use Intranet\Entities\Grupo;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateActividadTest extends DuskTestCase
{
    function test_create_post_actividad_validation()
    {
        $this->browse(function ($browser) {
            $browser->loginAs('021652470V')
                ->visit(route('actividad.create'))
                ->press('Guardar')
                ->assertRouteIs('actividad.create');
        });
    }

    function testAUserCreateAnActivity()
    {
        $this->browse(function ($browser) {
            $browser->loginAs('021652470V')
                ->visit(route('actividad.create'))
                ->type('name','Prueba')
                ->type('desde','04-08-2021 12:00')
                ->type('hasta','04-08-2021 15:00')
                ->press('Guardar')
                ->assertRouteIs('actividad.detalle',4   );
        });
    }
}