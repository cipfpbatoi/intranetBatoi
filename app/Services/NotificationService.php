<?php

namespace Intranet\Services;

use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;

class NotificationService
{
    private function receptor($id)
    {
        if (strlen($id) == 8) {
            return Alumno::find($id);
        }
        return Profesor::find($id);
    }

    private function emisor($emisor)
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


    public function send($id, $mensaje, $enlace = '#', $emisor = null)
    {
        $emisor = $this->emisor($emisor);
        $receptor = $this->receptor($id);
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
