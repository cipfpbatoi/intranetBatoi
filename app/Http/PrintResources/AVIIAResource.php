<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

class AVIIAResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'ANEXOVII-A.pdf';
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
            'SENYOR' => $secretario,
            'DIRECTOR' => $director,
            'SECRETARIO' => $secretario,
            'CENTRE' => config('contacto.nombre'),
            'CODI' => config('contacto.codi'),
            'EMPRESA' => $this->elements->Colaboracion->Centro->Empresa->nombre,
            'NIF' => $this->elements->Colaboracion->Centro->Empresa->cif,
            'CURSO' => curso(),
            'POBLACION' => config('contacto.poblacion'),
            'DIA' => day(Hoy()),
            'MES' => month(Hoy()),
            'AÑO' => substr(year(Hoy()), 2,2),
        ];
    }
}

