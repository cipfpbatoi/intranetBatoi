<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Fct as realFct;

class Fct extends ModelPoll
{
    public static function loadPoll($votes){

        $fcts = collect();
        foreach (realFct::misFctsColaboracion()->haEmpezado()->esFct()->whereNotIn('id',$votes)->get() as $fct) {
            $fcts->push(['option1'=>$fct]);
        }
        if (count($fcts)) return $fcts;
        return null;
    }
    public static function interviewed(){
        return 'Intranet\\Entities\\Profesor';
    }
    public static function keyInterviewed(){
        return 'dni';
    }
    public static function loadVotes($id)
    {
        $fcts = hazArray(realFct::misFctsColaboracion()->haEmpezado()->esFct()->get(),'id');
        $votes = Vote::getVotes($id,$fcts)->get();
        $classified = [];
        foreach ($votes as $vote){
            $classified[$vote->idOption1][$vote->option_id] = isset($vote->text)?$vote->text:$vote->value;
        }
        if (count($classified)) return $classified;
        return null;
    }
    public static function loadGroupVotes($id)
    {
        return [];
    }

    public static function has(){
        return realFct::misFctsColaboracion()->haEmpezado()->esFct()->count();
    }


}
