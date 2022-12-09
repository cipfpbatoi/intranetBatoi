<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

class ConformidadAlumnadoResource extends PrintResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray()
    {
        $alumno = $this->elements->Alumno;
        $tutor = AuthUser();
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        return [
            'untitled2' => "$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
            'untitled3' => config('contacto.nombre').' '.config('contacto.codi') ,
            'untitled4' => $grupo->Ciclo->vliteral,
            'untitled5' => "$tutor->fullName - DNI: $tutor->dni",
            'untitled6' => $alumno->fullName,
            'untitled7' => $alumno->fullName,
            'untitled10' => config('contacto.poblacion'),
            'untitled13' => day(Hoy()),
            'untitled11' => month(Hoy()),
            'untitled12' => substr(year(Hoy()), 2,2),
            'untitled8' => $alumno->fullName,
        ];
    }
}

