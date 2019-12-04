<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Profesor as ProfesorPoll;


class Profesor extends ProfesorPoll implements ModelPoll
{


    public static function loadPoll(){
        $modulos = collect();
        foreach (AuthUser()->Grupo as $grupo){
            foreach ($grupo->Modulos as $modulo){
                $modulos->push(['option1'=>$modulo,'option2'=>$modulo->Profesores()]);
            }
        }
        return $modulos;
    }
    public static function interviewed(){
        return 'Intranet\\Entities\\Alumno';
    }
    public static function keyInterviewed(){
        return 'nia';
    }


  
}
