<?php

namespace Intranet\Http\PrintResources;


use Intranet\Application\Grupo\GrupoService;


class A3ENResource extends PrintResource
{
    
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'AnexoIIIEN.pdf';
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
        $centro = $this->elements->Fct->Colaboracion->Centro;
        $empresa = $centro->Empresa;
        $instructor = $this->elements->Fct->instructor;

        return [
            'Text Box 1' => $alumno->fullName." (NIA: $alumno->nia)",
            'Text Box 3' => $this->elements->desde.' - '.$this->elements->hasta,
            'Text Box 1_3' => config('contacto.nombre').' '.config('contacto.codi'),
            'Text Box 1_2' => $tutor->fullName,
            'Text Box 2' => $grupo->Ciclo->vliteral,
            'Text Box 1_4' => $empresa->nombre." - ".$empresa->cif,
            'Text Box 7' => config('contacto.poblacion'),
            'Text Box 7_2' => day(Hoy()),
            'Text Box 7_3' => month(Hoy()),
            'Text Box 7_4' => substr(year(Hoy()), 2, 2),
            'Text Box 7_7' => $tutor->fullName,
            'Text Box 7_6' => $instructor->nombre,
            'Text Box 7_5' => $alumno->fullName
        ];
    }
}
