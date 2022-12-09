<?php

namespace Intranet\Http\PrintResources;


use Intranet\Entities\Grupo;


class A5Resource extends PrintResource
{
    
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = '5_Informe_consecucio_competencies_tutor.pdf';
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
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first();
        $telefonoAlumne = ($alumno->telef1 != '')?$alumno->telef1:$alumno->telef2;
        $centro = $this->elements->Fct->Colaboracion->Centro;
        $empresa = $centro->Empresa;
        $instructor = $this->elements->Fct->instructor;

        return [
            'untitled1' => $alumno->fullName." (NIA: $alumno->nia) - $alumno->dni",
            'untitled2' => "Tel $telefonoAlumne - $alumno->email",
            'untitled3' => config('contacto.nombre').' '.config('contacto.codi'),
            'untitled4' => "$tutor->fullName -$tutor->dni - Tel:* - $tutor->email",
            'untitled5' => $grupo->Ciclo->vliteral,
            'untitled6' => $empresa->nombre." - ".$empresa->cif,
            'untitled7' => "$centro->direccion , $centro->localidad ($centro->codiPostal ".provincia($centro->codiPostal).") - Tel: $centro->telefono - $centro->email",
            'untitled8' => $instructor->nombre.' - '.$instructor->dni.' - '.$instructor->email,
            'untitled9' => $this->elements->horas.' h',
            'untitled13' =>  config('contacto.poblacion'),
            'untitled14' => day(Hoy()),
            'untitled15' => month(Hoy()),
            'untitled16' => substr(year(Hoy()), 2, 2),
            'untitled18' => $tutor->fullName,
        ];
    }
}
