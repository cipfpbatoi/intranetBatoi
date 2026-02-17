<?php

namespace Intranet\Http\PrintResources;

use Intranet\Application\Grupo\GrupoService;

class NotificacioInspeccioResource extends PrintResource
{

    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'NotificacioInspeccio.pdf';
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
            'Texto1' => config('contacto.nombre'),
            'Texto2' => config('contacto.codi'),
            'Texto3' => $grupo->Ciclo->vliteral,"$alumno->fullName - NIA: $alumno->nia - DNI: $alumno->dni",
            'Texto4' => $alumno->nombre,
            'Texto5' => $alumno->apellido1.' '.$alumno->apellido2,
            'Texto6' => $alumno->nia,
            'Texto7' => $this->elements->desde,
            'Texto8' => $this->elements->hasta,
        ];
    }
}

