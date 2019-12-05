<?php

namespace Intranet\Entities\Poll;

class Profesor extends ModelPoll
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
}
