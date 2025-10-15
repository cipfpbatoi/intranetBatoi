<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

class AVIIIResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'ANEXOVIII.pdf';
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
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->largestByAlumnes()->first();
        $secretario = Profesor::find(config('avisos.secretario'))->fullName;
        $director = Profesor::find(config('avisos.director'))->fullName;
        return [
            'SECRET 1' => $secretario,
            'ALUMNE' => $alumno->fullName,
            'NIF'=> $alumno->dni,
            'CENTRE' => config('contacto.nombre'),
            'CODI'  => config('contacto.codi') ,
            'CICLE 2' => $grupo->Ciclo->vliteral,
            'HORAS' => $this->elements->horas,
            'HORES 1'=> $this->elements->horas,
            'PERIODE 1' => $this->elements->desde . ' a ' . $this->elements->hasta,
            'EMPRE 1' => $this->elements->Fct->Colaboracion->Centro->nombre,
            'POBLA' => config('contacto.poblacion'),
            'DIA' => day(Hoy()),
            'MES' => month(Hoy()),
            'ANY' => substr(year(Hoy()), 2,2),
            'SECRETARIA' => $secretario,
            'DIRECCIÓ' => $director
        ];
    }
}

