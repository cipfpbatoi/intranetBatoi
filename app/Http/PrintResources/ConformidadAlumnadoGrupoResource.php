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
        $data_actual = new \DateTime();
        $any_academic_inici = ($data_actual->format('m') < 6) ?
            $data_actual->format('Y') - 1 :
            $data_actual->format('Y');
        $primer_de_desembre = new \DateTime("December 1, $any_academic_inici");

        $array = [
            'Text1' => config('contacto.nombre').' '.config('contacto.codi') ,
            'Text3' => $grupo->Ciclo->vliteral,
            'Text2' => "$tutor->fullName - DNI: $tutor->dni",
            'Button70' => 'Yes',
            'Button72' => $data_actual < $primer_de_desembre?'Yes':'No',
            'Button73' => $data_actual < $primer_de_desembre?'No':'Yes',
            'Button79' => 'Yes',
            'Button81' => $data_actual < $primer_de_desembre?'Yes':'No',
            'Button82' => $data_actual < $primer_de_desembre?'No':'Yes',
            'Text66' => config('contacto.poblacion'),
            'Text67' => day(Hoy()),
            'Text68' => month(Hoy()),
            'Text69' => substr(year(Hoy()), 2,2),
        ];
        $i = 6;
        foreach ($this->getElements()??[] as $element) {
            $title = 'Text'.$i;
            $array[$title] = $element->Alumno->fullName??$element->fullName;
            $i += 2;
        }
        return $array;
    }
}

