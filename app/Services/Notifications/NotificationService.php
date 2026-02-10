<?php

namespace Intranet\Services\Notifications;

use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;
use Illuminate\Support\Facades\Schema;

class NotificationService
{
    private function receptor($id)
    {
        if (strlen($id) == 8) {
            if (!Schema::hasTable('alumnos')) {
                return null;
            }
            return Alumno::find($id);
        }
        if (!Schema::hasTable('profesores')) {
            return null;
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
            if (Schema::hasTable('profesores') && $user = Profesor::find($emisor)) {
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
