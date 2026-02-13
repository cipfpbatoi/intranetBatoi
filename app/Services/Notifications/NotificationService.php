<?php

namespace Intranet\Services\Notifications;

use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;
use Illuminate\Support\Facades\Schema;

class NotificationService
{
    /** @var callable */
    private $findAlumno;
    /** @var callable */
    private $findProfesor;
    /** @var callable */
    private $hasTable;
    /** @var callable */
    private $fechaProvider;

    public function __construct(
        ?callable $findAlumno = null,
        ?callable $findProfesor = null,
        ?callable $hasTable = null,
        ?callable $fechaProvider = null
    ) {
        $this->findAlumno = $findAlumno ?? static fn ($id) => Alumno::find($id);
        $this->findProfesor = $findProfesor ?? static fn ($id) => Profesor::find($id);
        $this->hasTable = $hasTable ?? static fn (string $table) => Schema::hasTable($table);
        $this->fechaProvider = $fechaProvider ?? static fn () => fechaString();
    }

    private function receptor($id)
    {
        if (strlen($id) == 8) {
            if (!(($this->hasTable)('alumnos'))) {
                return null;
            }
            return ($this->findAlumno)($id);
        }
        if (!(($this->hasTable)('profesores'))) {
            return null;
        }
        return ($this->findProfesor)($id);
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
        $fecha = ($this->fechaProvider)();
        if ($emisor && $receptor) {
            $receptor->notify(new mensajePanel(
                ['motiu' => $mensaje,
                    'emissor' => $emisor,
                    'data' => $fecha,
                    'enlace' => $enlace]));
        } else {
            if ((($this->hasTable)('profesores')) && $user = ($this->findProfesor)($emisor)) {
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
