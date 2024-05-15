<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

class NotificacioInspeccioResource extends PrintResource
{

    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'NotificacioInspeccio.pdf';
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
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $director = Profesor::find(config('avisos.director'))->fullName;
        return [
            'centre' => config('contacto.nombre').' '.config('contacto.codi'),
            'cicle' => $grupo->Ciclo->vliteral ,
            'codi' => "$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
            'nombre' => $director ,
            'cognoms' => $director,
            'untitled29' => config('contacto.poblacion'),
            'untitled31' => day(Hoy()),
            'untitled30' => month(Hoy()),
            'untitled32' => substr(year(Hoy()), 2, 2),
            'untitled33' => $director
        ];
    }
}

