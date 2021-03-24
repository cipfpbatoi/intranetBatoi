<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;
use Intranet\Entities\Comision;

class ShowComisionTest extends FeatureTestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_user_can_see_the_post_details()
    {
        $this->actingAs($this->defaultUser());
        $comision = $this->newModel(Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2021-04-04 08:00:00',
        'hasta' => '2021-04-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        ]);

        $this->visit(route('comision.show',['comision'=>$comision]))
             ->seeInElement('h2','Muestra Comisión id. '.$comision->id)
             ->see('COCHE')
             ->see('SEAT ALTEA XL');
    }
}
