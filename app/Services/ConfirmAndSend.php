<?php
namespace Intranet\Services;

class ConfirmAndSend
{
    public static function render($model, $id){
        $class = 'Intranet\\Entities\\'.$model;
        $element = $class::find($id);
        return view('intranet.confirm',compact('model','element','id'));
    }
}