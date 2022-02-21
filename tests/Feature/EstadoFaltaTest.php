<?php
namespace Tests\Feature;

use Tests\IntegrationTestCase;
use Intranet\Entities\Falta;


class EstadoFaltaTest extends IntegrationTestCase
{
    
    function test_alta_falta_sin_identificar()
    {
        $falta = $this->newModel(Falta::class,[
        'desde' =>'2017-08-04',
        'dia_completo' => 1,
        'motivos' => 1,
        'estado' => 0,
        'idProfesor' => $this->defaultUser()->dni,   
        ]);
        $this->notSeeInDatabase('faltas',[
            'desde' => '2017-08-04',
            'estado' => 0,
            'idProfesor' => $this->defaultUser()->dni
        ]);
    }
    function test_alta_falta_estado_1(){
        $this->actingAs($this->defaultUser());
        $falta = $this->newModel(Falta::class,[
        'desde' =>'2017-08-04',
        'dia_completo' => 1,
        'motivos' => 1,
        'estado' => 0
        ]);
        $falta->save();
        $this->seeInDatabase('faltas',[
            'desde' => '2017-08-04',
            'idProfesor' => $this->defaultUser()->dni,
            'estado' => '0'
        ]);
        Falta::putEstado($falta->id,1);
        $this->seeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => $this->defaultUser()->dni,
            'read_at' => NULL
        ]);
    }
    
}
