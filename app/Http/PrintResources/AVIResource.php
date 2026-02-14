<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;

class AVIResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'ANEXOVI-CONFORMIDADDELALUMNADO.pdf';
        $this->flatten = false;
    }

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
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->largestByAlumnes()->first();
        return [
            'ALUMNO' => "ALUMNO$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
            'CENTRE' => config('contacto.nombre').' '.config('contacto.codi') ,
            'CICLO' => $grupo->Ciclo->vliteral,
            'TUTOR' => "$tutor->fullName - DNI: $tutor->dni",
            'POBLA' => config('contacto.poblacion'),
            'DIA' => day(Hoy()),
            'MES' => month(Hoy()),
            'AÑO' => substr(year(Hoy()), 2,2),
            'ALUMNA' => $alumno->fullName,
        ];
    }
}

