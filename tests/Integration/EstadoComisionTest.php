<?php
namespace Tests\Integration;

use Tests\IntegrationTestCase;
use Intranet\Entities\Comision;

class EstadoComisionTest extends IntegrationTestCase
{
  
    
    function test_alta_comision_sin_identificar()
    {
        $comision = $this->newModel(\Intranet\Entities\Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2017-08-04 08:00:00',
        'hasta' => '2017-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        'idProfesor' => '021642470V',   
        ]);
        $this->notSeeInDatabase('comisiones',[
            'desde' => '2017-08-04 08:00:00',
            'estado' => '0',
            'idProfesor' => '021642470V'
        ]);
    }
    function test_estado_0(){
        $this->actingAs($this->defaultUser());
        $comision = $this->newModel(\Intranet\Entities\Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2017-08-04 08:00:00',
        'hasta' => '2017-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        'estado' => 2,
        ]);
        $comision->save();
        $this->seeInDatabase('comisiones',[
           'servicio' => 'Prueba',
            'desde' => '2017-08-04 08:00:00',
            'idProfesor' => $this->defaultUser()->dni,
            'estado' => '2'
        ]);
        $this->notSeeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => '020003898Q',
            'notifiable_type' => 'Intranet\Entities\Profesor',
            'read_at' => NULL
        ]);
        Comision::putEstado($comision->id,0);
        $this->seeInDatabase('comisiones',[
           'servicio' => 'Prueba',
            'desde' => '2017-08-04 08:00:00',
            'estado' => 0,
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->seeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => $this->defaultUser()->dni,
            'read_at' => NULL
        ]);
    }
    function test_estado_1(){
        $this->actingAs($this->defaultUser());
        $comision = $this->newModel(\Intranet\Entities\Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2017-08-04 08:00:00',
        'hasta' => '2017-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        ]);
        $comision->save();
        $this->seeInDatabase('comisiones',[
            'desde' => '2017-08-04 08:00:00',
            'estado' => '0',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->notSeeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => config('contacto.director'),
            'read_at' => NULL
        ]);
        Comision::putEstado($comision->id,1);
        $this->seeInDatabase('comisiones',[
            'desde' => '2017-08-04 08:00:00',
            'estado' => '1',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->seeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => config('contacto.director'),
            'read_at' => NULL
        ]);
    }
    function test_estado_3(){
        $this->actingAs($this->defaultUser());
        $comision = $this->newModel(\Intranet\Entities\Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2017-08-04 08:00:00',
        'hasta' => '2017-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        ]);
        $comision->save();
        Comision::putEstado($comision->id,3);
        $this->seeInDatabase('comisiones',[
            'desde' => '2017-08-04 08:00:00',
            'estado' => '3',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->seeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => $this->defaultUser()->dni,
            'read_at' => NULL
        ]);
    }
    
    
    
    
    
    function test_estado_4(){
        $this->actingAs($this->defaultUser());
        $comision = $this->newModel(\Intranet\Entities\Comision::class,[
        'servicio' => 'Prueba',
        'desde' =>'2017-08-04 08:00:00',
        'hasta' => '2017-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
        ]);
        $comision->save();
        $this->notSeeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => config('contacto.secretario'),
            'read_at' => NULL
        ]);
        Comision::putEstado($comision->id,4);
        $this->seeInDatabase('comisiones',[
            'desde' => '2017-08-04 08:00:00',
            'estado' => '4',
            'idProfesor' => $this->defaultUser()->dni
        ]);
        $this->seeInDatabase('notifications', [
            'type' => 'Intranet\Notifications\mensajePanel',
            'notifiable_id' => config('contacto.secretario'),
            'read_at' => NULL
        ]);
    }
    
}
