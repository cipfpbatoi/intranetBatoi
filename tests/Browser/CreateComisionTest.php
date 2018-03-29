<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateComisionTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    
    function test_a_user_create_a_comision_2()
    {
        
        $this->browse(function ($browser){
            $browser->loginAs($user = $this->defaultUser())
                    ->visitRoute('comision.create')
                ->type('servicio','Prueba')
                ->type('desde','04-08-2017 08:00')
                ->type('hasta','04-08-2017 14:00')
                ->type('medio','COCHE')
                ->type('marca','SEAT ALTEA XL')
                ->type('matricula','1023-HDX')
                ->type('kilometraje',450)
        ->press('Guardar') ;});
        
        $this->assertDatabaseHas('comisiones',[
           'servicio' => 'Prueba',
            'desde' => '2017-08-04 08:00:00',
            'estado' => '0',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->see('Solicitud autorización');
        
    }
}
