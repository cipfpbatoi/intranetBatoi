<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;

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
        $director = cargo('director')->fullName;
        $secretario = cargo('secretario')->fullName;
        $colaboracion = $this->elements->Colaboracion;
        $centro = $colaboracion?->Centro;
        $empresa = $centro?->Empresa;

        $dades_fct = [
            'SECRETARI' => $secretario,
            'DIRECTOR' => $director,
            'SECRETARIO' => $secretario,
            'CENTRE' => config('contacto.nombre'),
            'CODI' => config('contacto.codi'),
            'EMPRESA' => $empresa?->nombre,
            'NIF' => $empresa?->cif,
            'CURS' => curso(),
            'POBLACIÓN' => config('contacto.poblacion'),
            'DIA' => day(Hoy()),
            'MES' => month(Hoy()),
            'AÑO' => substr(year(Hoy()), 2,2),
            'ADRES 1' => $centro?->direccion,
            'LOCA 1' => $centro?->localidad,
            'NUM ALU 1' => $this->elements->AlFct->count(),
            'TOT HOR 1' => $this->elements->AlFct->sum('horas'),
        ];
        return $dades_fct;
    }
}
