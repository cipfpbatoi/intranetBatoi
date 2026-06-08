<?php
namespace Intranet\Services\Notifications;

/**
 * Renderitza la pantalla intermèdia de confirmació abans d'enviar un element.
 */
class ConfirmAndSend
{
    /**
     * Mostra una confirmació explícita abans de canviar l'estat d'un element.
     *
     * @param string $model Nom curt del model.
     * @param int|string $id Identificador de l'element.
     * @param string|null $message Títol del pas de confirmació.
     * @param string|null $route URL que confirma l'enviament.
     * @param string|null $back URL de tornada sense enviar.
     * @param string|null $notice Text explicatiu addicional.
     * @param string $confirmText Text del botó de confirmació.
     * @param string $cancelText Text del botó de cancel·lació.
     * @return \Illuminate\Contracts\View\View
     */
    public static function render(
        $model,
        $id,
        $message = null,
        $route = null,
        $back = null,
        $notice = null,
        $confirmText = 'SI',
        $cancelText = 'NO'
    ) {
        $class = 'Intranet\\Entities\\'.$model;
        $element = $class::find($id);
        $route = $route??"/".strtolower($model)."/$id/init/";
        $back = $back??"/".strtolower($model)."/";
        $message = $message ?? "Enviar $model a Direcció";
        return view('intranet.confirm', compact(
            'element',
            'message',
            'route',
            'back',
            'notice',
            'confirmText',
            'cancelText'
        ));
    }
}
