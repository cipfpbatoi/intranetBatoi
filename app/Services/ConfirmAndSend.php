<?php

namespace Intranet\Services;

use Intranet\Entities\Falta;

class ConfirmAndSend
{


    public static function render($model, $id,$route){
        $class = 'Intranet\\Entities\\'.$model;
        $element = $class::find($id);
        return view('intranet.confirm',compact('model','element','id','route'));
    }

    public static function confirm(Request $request){

    }
}