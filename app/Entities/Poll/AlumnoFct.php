<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Fct;

class AlumnoFct extends ModelPoll
{
    public static function loadPoll(){
        $alumno = AuthUser();
        $fcts = collect();
        foreach ($alumno->fcts()->where('asociacion',1)->get() as $fct) {
            $fcts->push(['option1'=>$fct]);
        }
        return $fcts;
    }

    public static function loadVotes($id)
    {
        $fcts = hazArray(Fct::misFcts()->get(),'id');
        $votes = Vote::getVotes($id,$fcts)->get();
        foreach ($votes as $vote){
            $classified[$vote->idOption1][$vote->option_id] = isset($vote->text)?$vote->text:$vote->value;
        }
        return $classified;
    }
    public static function loadGroupVotes($id)
    {
        return [];
    }
    public static function vista(){
        return 'Fct';
    }
    public static function has(){
        return count(AuthUser()->fcts);
    }
}
