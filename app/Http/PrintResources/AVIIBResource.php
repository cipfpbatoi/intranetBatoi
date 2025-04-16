<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

class AVIIBResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'ANEXOVII-B.pdf';
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
        $director = Profesor::find(config('avisos.director'))->fullName;
        $secretario = Profesor::find(config('avisos.secretario'))->fullName;
        return [
            'SECRETARI' => $secretario,
            'DIRECTOR' => $director,
            'SECRETARIO' => $secretario,
            'CENTRE' => config('contacto.nombre'),
            'CODI' => config('contacto.codi'),
            'EMPRESA' => $this->elements->Colaboracion->Centro->Empresa->nombre,
            'NIF' => $this->elements->Instructor->dni,
            'ENSE 2' => $this->elements->Colaboracion->Ciclo->vliteral,
            'INSTRUCT' => $this->elements->Instructor->name,
            'INSTRUCT 2' => $this->elements->Instructor->surnames,
            'CURS' => curso(),
            'POBLACIÓN' => config('contacto.poblacion'),
            'DIA' => day(Hoy()),
            'MES' => month(Hoy()),
            'AÑO' => substr(year(Hoy()), 2,2),
            'ALUMNES' => $this->elements->AlFct->count(),
            'HORES' => $this->elements->AlFct->sum('horas'),
        ];
    }
}

