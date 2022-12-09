<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

class ConformidadTutoriaResource extends PrintResource
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
            'untitled1' => "$tutor->fullName - DNI: $tutor->dni",
        'untitled2' => "$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
        'untitled3' => config('contacto.nombre').' '.config('contacto.codi') ,
        'untitled4' => $grupo->Ciclo->vliteral ,

        'untitled5' => $tutor->fullName,
        'untitled6' => $tutor->fullName,
        'untitled29' => config('contacto.poblacion'),
        'untitled31' => day(Hoy()),
        'untitled30' => month(Hoy()),
        'untitled32' => substr(year(Hoy()),2,2),
        'untitled33' => $tutor->fullName,
        ];
    }
}

