<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Fct as FctPoll;

class Fct extends FctPoll implements ModelPoll
{
    public static function loadPoll(){
        $fcts = collect();
        foreach (Fct::misFcts()->get() as $fct) {
            $fcts->push(['option1'=>$fct]);
        };
        return $fcts;
    }
    public static function interviewed(){
        return 'Intranet\\Entities\\Profesor';
    }
    public static function keyInterviewed(){
        return 'dni';
    }
}
