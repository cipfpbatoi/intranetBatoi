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
            'Text1' => config('contacto.nombre').' '.config('contacto.codi') ,
            'Text3' => $grupo->Ciclo->vliteral,
            'Text2' => "$tutor->fullName - DNI: $tutor->dni",
            'Text66' => config('contacto.poblacion'),
            'Text67' => day(Hoy()),
            'Text68' => month(Hoy()),
            'Text69' => substr(year(Hoy()), 2,2),
        ];
        $i = 6;
        foreach ($this->getElements()??[] as $element) {
            $title = 'Text'.$i;
            $array[$title] = $element->Alumno->fullName;
            $i += 2;
        }
        return $array;
    }
}

