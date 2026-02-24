<?php

declare(strict_types=1);

namespace Intranet\Services\School;

use Intranet\Entities\Asistencia;
use Intranet\Entities\Reunion;

class ReunionService
{
    public function makeMessage(Reunion $reunion): string
    {
        if (haVencido($reunion->fecha)) {
            return "Ja està disponible l'acta de la reunió {$reunion->descripcion} del dia {$reunion->fecha}";
        }

        $espacio = $reunion->Espacio->descripcion ?? '';

        return "Estas convocat a la reunió:  {$reunion->descripcion} el dia {$reunion->fecha} a {$espacio}";
    }

    public function addProfesor(Reunion $reunion, string $idProfesor): void
    {
        $reunion->profesores()->syncWithoutDetaching([$idProfesor => ['asiste' => 1]]);
    }

    public function removeProfesor(Reunion $reunion, string $idProfesor): void
    {
        $reunion->profesores()->detach($idProfesor);
    }

    public function addAlumno(Reunion $reunion, string $idAlumno, int $capacitats): void
    {
        $reunion->alumnos()->syncWithoutDetaching([$idAlumno => ['capacitats' => $capacitats]]);
    }

    public function removeAlumno(Reunion $reunion, string $idAlumno): void
    {
        $reunion->alumnos()->detach($idAlumno);
    }

    public function notify(Reunion $reunion): void
    {
        $message = $this->makeMessage($reunion);
        $url = "/reunion/{$reunion->id}/pdf";

        foreach (Asistencia::query()->where('idReunion', $reunion->id)->get() as $profesor) {
            avisa($profesor->idProfesor, $message, $url);
        }
    }
}

