<?php

namespace Intranet\Componentes;

use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;

class Mensaje
{
    private static function receptor($id)
    {
        if (strlen($id) == 8) {
            return Alumno::find($id);
        }
        return Profesor::find($id);
    }

    private static function emisor($emisor)
    {
        if ($emisor) {
            return $emisor;
        }
        if (authUser()) {
            return authUser()->shortName;
        }
        if (apiAuthUser()) {
            return apiAuthUser()->shortName;
        }
    }


    public static function send($id, $mensaje, $enlace = '#', $emisor = null)
    {
        $emisor = self::emisor($emisor);
        $receptor = self::receptor($id);
        $fecha = fechaString();
        if ($emisor && $receptor) {
            $receptor->notify(new mensajePanel(
                ['motiu' => $mensaje,
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        } else {
            if ($user = Profesor::find($emisor)) {
                $user->notify(new mensajePanel(
                    [
                        'motiu' => "No trobe usuari $id",
                        'emissor' => $emisor,
                        'data' => $fecha,
                        'enlace' => $enlace
                    ]));
            }
        }
    }
}
