<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;

class AutorizacionGrupoResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = '10b_Conformitat_tutoria_per_a_grups.pdf';
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray()
    {
        $nomTutor = AuthUser()->fullName;
        $dni = AuthUser()->dni;
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $alumnes = '';
        $mes = mes(Hoy());

        foreach ($this->getElements()??[] as $element) {
            $alumnes .= $element->Alumno->fullName.'
';
        }
        return [
            'untitled1' => $nomTutor.' - '.$dni,
            'untitled2' => config('contacto.nombre').' '.config('contacto.codi'),
            'untitled3' => $grupo->curso.' '.$grupo->Ciclo->vliteral.' - '.$grupo->Ciclo->ciclo,
            'untitled4' => $nomTutor,
            'untitled6' => $nomTutor,
            'untitled8' => 'Yes',
            'untitled17' => 'Yes',
            'untitled28' => $alumnes,
            'untitled29' => config('contacto.poblacion'),
            'untitled30' => day(Hoy()),
            'untitled31' => month(Hoy()),
            'untitled32' => substr(year(Hoy()), 2, 2),
            'untitled33' => '',
            'untitled34' => $nomTutor,
            'untitled11' => ($mes<=4)?'Yes':'No',
            'untitled20' => ($mes<=4)?'Yes':'No',
            'untitled12' => ($mes>4&&$mes<=8)?'Yes':'No',
            'untitled21' => ($mes>4&&$mes<=8)?'Yes':'No',
            'untitled10' => ($mes>8)?'Yes':'No',
            'untitled19' => ($mes>8)?'Yes':'No',
        ];
    }
}

