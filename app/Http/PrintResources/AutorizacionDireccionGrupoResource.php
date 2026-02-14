<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;

class AutorizacionDireccionGrupoResource extends PrintResource
{

    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = '9b_Autoritzacio_direccio_situacions_excepcionals_per_a_grups.pdf';
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
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->largestByAlumnes()->first();
        $director = cargo('director')->fullName;
        $alumnes = '';
        $data_actual = new \DateTime();
        $any_academic_inici =
            ($data_actual->format('m') < 6) ?
                $data_actual->format('Y') - 1 :
                $data_actual->format('Y');
        $primer_de_desembre = new \DateTime("December 1, $any_academic_inici");

        foreach ($this->getElements()??[] as $element) {
            $alumnes .= $element->Alumno->fullName??$element->fullName.'
';
        }
        return [
            'untitled1' => config('contacto.nombre').' '.config('contacto.codi'),
            'untitled2' => $grupo->Ciclo->vliteral ,
            'untitled3' => $grupo->curso.' '.$grupo->Ciclo->vliteral.' - '.$grupo->Ciclo->ciclo,
            'untitled4' => $director ,
            'untitled6' => $director,
            'untitled8' => 'Yes',
            'untitled10' => $data_actual < $primer_de_desembre?'Yes':'No',
            'untitled11' => $data_actual < $primer_de_desembre?'No':'Yes',
            'untitled17' => 'Yes',
            'untitled19' => $data_actual < $primer_de_desembre?'Yes':'No',
            'untitled20' => $data_actual < $primer_de_desembre?'No':'Yes',
            'untitled28' => $alumnes,
            'untitled29' => "L'alumnat no interrumpisca les pràctiques per les festes escolars per tal de garantir la continuïtat formativa i la inserció en el món laboral.",
            'untitled30' => config('contacto.poblacion'),
            'untitled31' => day(Hoy()),
            'untitled32' => month(Hoy()),
            'untitled33' => substr(year(Hoy()), 2, 2),
            'untitled34' => $director
        ];
    }
}

