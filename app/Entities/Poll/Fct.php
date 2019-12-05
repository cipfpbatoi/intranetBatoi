<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Fct as realFct;

class Fct extends ModelPoll
{
    public static function loadPoll(){
        $fcts = collect();
        foreach (realFct::misFcts()->get() as $fct) {
            $fcts->push(['option1'=>$fct]);
        }
        return $fcts;
    }
    public static function interviewed(){
        return 'Intranet\\Entities\\Profesor';
    }
    public static function keyInterviewed(){
        return 'dni';
    }
    public static function loadVotes($id)
    {
        $fcts = hazArray(realFct::misFcts()->get(),'id');
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
}
