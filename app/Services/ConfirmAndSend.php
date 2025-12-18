<?php
namespace Intranet\Services;

/**
 * Servei ConfirmAndSend.
 */
class ConfirmAndSend
{
    public static function render($model, $id, $message=null, $route=null, $back=null)
    {
        $class = 'Intranet\\Entities\\'.$model;
        $element = $class::find($id);
        $route = $route??"/".strtolower($model)."/$id/init/";
        $back = $back??"/".strtolower($model)."/";
        $message = $message ?? "Enviar $model a Direcció";
        return view('intranet.confirm', compact('element', 'message', 'route', 'back'));
    }
}
