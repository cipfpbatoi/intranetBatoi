<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;

class ConformidadAlumnadoGrupoResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = '11b_Conformitat_alumnat_grups.pdf';
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
        $tutor = AuthUser();
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $array = [
            'untitled1' => config('contacto.nombre').' '.config('contacto.codi') ,
            'untitled2' => $grupo->Ciclo->vliteral,
            'untitled3' => "$tutor->fullName - DNI: $tutor->dni",
            'untitled84' => config('contacto.poblacion'),
            'untitled85' => day(Hoy()),
            'untitled86' => month(Hoy()),
            'untitled87' => substr(year(Hoy()), 2,2),
        ];
        $i = 24;
        foreach ($this->getElements()??[] as $element) {
            $title = 'untitled'.$i;
            $array[$title] = $element->Alumno->fullName;
            $i += 2;
        }
        return $array;
    }
}

