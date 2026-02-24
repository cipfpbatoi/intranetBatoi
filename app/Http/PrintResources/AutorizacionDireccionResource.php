<?php

namespace Intranet\Http\PrintResources;

use Intranet\Application\Grupo\GrupoService;

class AutorizacionDireccionResource extends PrintResource
{

    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = '9_Autoritzacio_direccio_situacions_excepcionals.pdf';
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
        $grupo = app(GrupoService::class)->largestByTutor(AuthUser()->dni);
        $director = cargo('director')->fullName;
        return [
            'untitled1' => config('contacto.nombre').' '.config('contacto.codi'),
            'untitled2' => $grupo->Ciclo->vliteral ,
            'untitled3' => "$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
            'untitled4' => $director ,
            'untitled8' => $director,
            'untitled29' => config('contacto.poblacion'),
            'untitled31' => day(Hoy()),
            'untitled30' => month(Hoy()),
            'untitled32' => substr(year(Hoy()), 2, 2),
            'untitled33' => $director
        ];
    }
}

