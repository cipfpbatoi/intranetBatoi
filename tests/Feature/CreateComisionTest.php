<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;


class CreateComisionTest extends FeatureTestCase
{
    function test_user_guest_comision()
    {
        $this->visit(route('comision.create'));
        $this->seePageIs(route('login'));
    }
    
    function test_a_user_create_a_comision()
    {
        
        $this->actingAs($this->defaultUser());
        
        $this->visit(route('comision.create'))
                ->type('Prueba','servicio')
                ->type('04-08-2017 08:00','desde')
                ->type('04-08-2017 14:00','hasta')
                ->type('COCHE','medio')
                ->type('SEAT ALTEA XL','marca')
                ->type('1023-HDX','matricula')
                ->type(450,'kilometraje')
                ->press('Guardar') ;
        
        $this->seeInDatabase('comisiones',[
           'servicio' => 'Prueba',
            'desde' => '2017-08-04 08:00:00',
            'estado' => '0',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->see('Solicitud autorización');
        
    }
    function test_create_post_comision_validation()
    {
        $this->actingAs($this->defaultUser())
                ->visit(route('comision.create'))
                ->press('Guardar')
                ->seePageIs(route('comision.create'))
                ->seeErrors([
                   'required' => 'servicio',
                   'required' => 'marca',
                   'required' => 'matricula',
                   'required' => 'medio',
                    'after' => ['attribute' => 'hasta', 'date' => 'desde']
                ]);
    }
    
    
}
