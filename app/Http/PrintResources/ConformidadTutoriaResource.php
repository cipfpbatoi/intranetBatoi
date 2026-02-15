<?php

namespace Intranet\Http\PrintResources;

use Intranet\Application\Grupo\GrupoService;

class ConformidadTutoriaResource extends PrintResource
{
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = '10_Conformitat_tutoria.pdf';
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
        $tutor = AuthUser();
        $grupo = app(GrupoService::class)->largestByTutor(AuthUser()->dni);
        return [
            'untitled1' => "$tutor->fullName - DNI: $tutor->dni",
        'untitled2' => "$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
        'untitled3' => config('contacto.nombre').' '.config('contacto.codi') ,
        'untitled4' => $grupo->Ciclo->vliteral ,

        'untitled5' => $tutor->fullName,
        'untitled6' => $tutor->fullName,
        'untitled29' => config('contacto.poblacion'),
        'untitled31' => day(Hoy()),
        'untitled30' => month(Hoy()),
        'untitled32' => substr(year(Hoy()), 2,2),
        'untitled33' => $tutor->fullName,
        ];
    }
}

