<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;
use Intranet\Entities\Actividad;
use Intranet\Entities\Grupo;

class CreateActividadTest extends FeatureTestCase
{
    function test_user_guest_actividad()
    {
        $this->visit(route('actividad.create'));
        $this->seePageIs(route('login'));
    }
    
    function test_create_post_actividad_validation()
    {
        $this->actingAs($this->defaultUser())
                ->visit(route('actividad.create'))
                ->press('Guardar')
                ->seePageIs(route('actividad.create'))
                ->seeErrors([
                   'required' => 'name',
                   'after' => ['attribute' => 'hasta', 'date' => 'desde']
                ]);
    }
    
    function test_a_user_create_a_actividad()
    {
        
        
        $this->actingAs($user = $this->defaultTutor());
        
        $this->visit(route('actividad.create'))
                ->type('Prueba','name')
                ->type('04-08-2019 12:00','desde')
                ->type('04-08-2019 15:00','hasta')
                ->press('Guardar') ;
        
        $this->seeInDatabase('actividades',[
            'name' => 'Prueba',
            'desde' => '2019-08-04 12:00',
            'estado' => '0',
        ]);
        $siguiente = $this->siguiente('actividades');
        $this->seeInDatabase('actividad_profesor',[
            'idProfesor' => $user->dni,
            'coordinador' => 1,
            'idActividad' => $siguiente-1,
        ]);
        $this->seeInDatabase('actividad_grupo',[
            'idGrupo' => Grupo::Qtutor($user->dni)->first()->codigo,
            'idActividad' => $siguiente-1,
        ]);
        $this->see('Detalle actividad Prueba')
             ->select('1CFMA','idGrupo')
             ->press('Añadir Grupo');
        $this->seeInDatabase('actividad_grupo',[
            'idGrupo' => '1CFMA',
            'idActividad' => $siguiente-1,
        ]);
        $this->select('021652470V','idProfesor')
             ->press('Añadir Profesor');
        $this->seeInDatabase('actividad_profesor',[
            'idProfesor' => '021652470V',
            'idActividad' => $siguiente-1,
            'coordinador' => 0
        ]);
        $this->click("co_021652470V");
        $this->seeInDatabase('actividad_profesor',[
            'idProfesor' => '021652470V',
            'idActividad' => $siguiente-1,
            'coordinador' => 1
        ]);
        $this->seeInDatabase('actividad_profesor',[
            'idProfesor' => $user->dni,
            'coordinador' => 0,
            'idActividad' => $siguiente-1
        ]);
        $this->click("de_021652470V");
        $this->notSeeInDatabase('actividad_profesor',[
            'idProfesor' => '021652470V',
            'idActividad' => $siguiente-1,
            'coordinador' => 1
        ]);
        $this->seeInDatabase('actividad_profesor',[
            'idProfesor' => $user->dni,
            'coordinador' => 1,
            'idActividad' => $siguiente-1
        ]);
        $this->click("de_$user->dni");
        $this->SeeInDatabase('actividad_profesor',[
            'idProfesor' => $user->dni,
            'coordinador' => 1,
            'idActividad' => $siguiente-1
        ]);
        $this->see('No es pot donar de baixa el últim profesor');
    }
}
