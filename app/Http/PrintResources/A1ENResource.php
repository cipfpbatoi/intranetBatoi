<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Profesor;


class A1ENResource extends PrintResource
{
    
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'AnexoIEN.pdf';
        $this->flatten = true;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray()
    {
        $empresa = $this->elements->Fct->Colaboracion->Centro->Empresa;
        $director = Profesor::find(config('contacto.director'));

        return [
            'Text1' => config('contacto.codi'),
            'Text2' => year(Hoy()),
            'Text3' => $empresa->concierto,
            'Text4' => day(Hoy()),
            'Text5' => month(Hoy()),
            'Text6' => substr(year(Hoy()), 2, 2),
            'Text7' => $director->fullName.' - '.$director->dni,
            'Text8' => config('contacto.nombre').' - '.config('contacto.codi'),
            'Text9' =>
                config('contacto.direccion').'('.
                config('contacto.localidad').') - '.
                config('contacto.email').' - '.
                config('contacto.telefono'),
            'Text10' => $empresa->gerente,
            'Text11' => $empresa->nombre.' '.$empresa->cif,
            'Text12' => $empresa->direccion.'('.$empresa->localidad.') - '.$empresa->email.' - '.$empresa->telefono,
            'Text13' => $empresa->email,
            'Text14' => $empresa->email,
            'Text15' => config('contacto.localidad'),
            'Text16' => day(Hoy()),
            'Text17' => month(Hoy()),
            'Text18' => substr(year(Hoy()), 2, 2),
            'Text20' => $director->fullName,
            'Text22' => $director->gerente,
        ];
    }
}
