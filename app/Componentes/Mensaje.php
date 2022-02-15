<?php

namespace Intranet\Componentes;

use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;

class Mensaje
{
    private static function receptor($id){
        if (strlen($id) == 8) {
            return Alumno::find($id);
        }
        return Profesor::find($id);
    }

    private static function emisor($emisor){
        if ($emisor) {
            return $emisor;
        }
        if (AuthUser()) {
            return AuthUser()->shortName;
        }
        if (apiAuthUser()) {
            return apiAuthUser()->shortName;
        }
    }


    static public function send($id, $mensaje, $enlace = '#', $emisor = null)
    {
        $emisor = self::emisor($emisor);
        $receptor = self::receptor($id);
        $fecha = FechaString();

        if ($emisor && $receptor) {
            $receptor->notify(new mensajePanel(
                ['motiu' => $mensaje,
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        } else {
            AuthUser()->notify(new mensajePanel(
                ['motiu' => "No trobe usuari $id",
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        }
    }
}