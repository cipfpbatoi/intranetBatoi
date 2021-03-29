<?php
/**
 * Created by PhpStorm.
 * Profesor: igomis
 * Date: 2019-12-04
 * Time: 06:50
 */

namespace Intranet\Entities\Poll;


abstract class ModelPoll
{
    public static function loadPoll($votes){}
    public static function loadVotes($id){}
    public static function loadGroupVotes($id){}
    public static function interviewed(){
        return 'Intranet\\Entities\\Alumno';
    }
    public static function keyInterviewed(){
        return 'nia';
    }
    public static function vista(){
        return class_basename(static::class);
    }
    public static function aggregate(&$votes,$option1,$option2){

    }

    public static function has(){
        return true;
    }
}