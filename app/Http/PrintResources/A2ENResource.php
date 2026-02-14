<?php

namespace Intranet\Http\PrintResources;

use Intranet\Entities\Grupo;


class A2ENResource extends PrintResource
{
    
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'AnexoIIEN.pdf';
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
        $empresa = $this->elements->Fct->Colaboracion->Centro->Empresa;
        $director = cargo('director');
        $tutor = AuthUser();
        $alumno = $this->elements->Alumno;
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->largestByAlumnes()->first();
        $instructor = $this->elements->Fct->instructor;

        return [
            'Text Box 1' => config('contacto.nombre').' - '.config('contacto.codi'),
            'Text Box 1_3' => config('contacto.direccion').'('.
                config('contacto.poblacion').') - '.
                config('contacto.email').' - '.
                config('contacto.telefono'),
            'Text Box 1_2' => $tutor->fullName." - ".$tutor->telefono." - ".$tutor->email,
            'Text Box 1_4' => $alumno->fullName." - ".$alumno->email." - ".$alumno->telefono,
            'Text Box 2' => $grupo->Ciclo->vliteral,
            'Text Box 1_5' => $empresa->nombre.' '.$empresa->cif,
            'Text Box 1_6' =>
                $empresa->direccion.
                '('.$empresa->localidad.') - '.
                $empresa->email.' - '.
                $empresa->telefono,
            'Text Box 1_7' => $empresa->concierto,
            'Text Box 1_8' =>
                $empresa->direccion.
                '('.$empresa->localidad.') - '.
                $empresa->email.' - '.
                $empresa->telefono,
            'Text Box 1_9' => $instructor->nombre.' - '.$instructor->email.' - '.$instructor->telefono,
            'Text Box 3' => $this->elements->desde.' - '.$this->elements->hasta,
            'Text Box 5' => $this->elements->horas,
            'Text Box 7' => config('contacto.poblacion'),
            'Text Box 7_2' => day(Hoy()),
            'Text Box 7_3' => month(Hoy()),
            'Text Box 7_4' => substr(year(Hoy()), 2, 2),
            'Text Box 7_5' => $director->fullName,
            'Text Box 7_6' => $tutor->fullName,
            'Text Box 7_7' => $empresa->gerente,
        ];
    }
}
