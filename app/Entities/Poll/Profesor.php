<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;

class Profesor extends ModelPoll
{
    public static function loadPoll($votes){
        if (count($votes)) return null;
        $modulos = collect();
        foreach (AuthUser()->Grupo as $grupo){
            foreach ($grupo->Modulos as $modulo){
                $modulos->push(['option1'=>$modulo,'option2'=>$modulo->Profesores()]);
            }
        }
        return $modulos;
    }

    public static function loadVotes($id)
    {
        foreach (Modulo_grupo::misModulos() as $modulo) {
            $myVotes[$modulo->ModuloCiclo->Modulo->literal][$modulo->Grupo->codigo] = Vote::myVotes($id, $modulo->id)->get();
        }
        return $myVotes;
    }

    public static function loadGroupVotes($id)
    {
        foreach (Grupo::misGrupos()->get() as $grup){
            $modulos = hazArray(Grupo::find($grup->codigo)->Modulos,'id');
            $myGroupsVotes[$grup->codigo] = Vote::myGroupVotes($id,$modulos)->get();
        }
        return $myGroupsVotes;
    }
    public static function has(){
        return count(AuthUser()->Grupo);
    }

}
