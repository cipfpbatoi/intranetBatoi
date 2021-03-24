<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;
use Intranet\Entities\Falta;

class CreateFaltaTest extends FeatureTestCase
{

    function test_user_guest_falta()
    {
        $this->visit(route('falta.create'))
            ->seeHeader('status',401);
    }
    
    function test_a_user_create_a_falta()
    {
        $this->actingAs($this->defaultUser());
        
        $this->visit(route('falta.create'))
                ->type($this->defaultUser()->dni,'idProfesor')
                ->type('04-08-2017','desde')
                ->type(1,'dia_completo')
                ->type(1,'motivos')
                ->press('Guardar') ;
        
        $this->seeInDatabase('faltas',[
            'dia_completo' => 1,
            'desde' => '2017-08-04',
            'estado' => '0',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->see('Comunicación de Ausencia');
        $this->see('No enviada');
    }
}
