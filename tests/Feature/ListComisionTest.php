<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;
use Intranet\Entities\Comision;


class ListComisionTest extends FeatureTestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_user_can_see_the_comision_list_and_go_to_details()
    {
        $this->actingAs($user = $this->defaultUser());
        $comision = $this->newModel(Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2019-08-04 08:00:00',
        'hasta' => '2019-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        'idProfesor' => $user->dni,   
        ]);
        $comision->save();
        $this->visit('/comision')
                ->seeInElement('h2','Gestionar comisiones Servicio')
                ->see($comision->servicio)
                ->click('edit'.$comision->id)
                ->seeInField('servicio_id','Prueba')
                ->type('Ya no es una prueba','servicio')
                ->press('Guardar');
        $this->seeInDatabase('comisiones',[
           'servicio' => 'Ya no es una prueba',
            'desde' => '2019-08-04 08:00:00',
            'estado' => '0',
            'idProfesor' => $this->defaultUser()->dni
        ]);
                //->click('delete'.$comision->id)
                //->press('')
                        
        

    }
}
