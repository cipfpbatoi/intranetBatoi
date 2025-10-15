<?php

namespace Intranet\Http\PrintResources;


use Intranet\Entities\Grupo;

class ExempcioResource extends PrintResource
{
    
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'InformeExencionFCT.pdf';
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
        $grupo = Grupo::where('tutor', '=', AuthUser()->dni)->largestByAlumnes()->first();
        $telefonoAlumne = ($alumno->telef1 != '')?$alumno->telef1:$alumno->telef2;
        $telefonoTutor = ($tutor->movil1 != '')?$tutor->movil1:$tutor->movil2;
        
        return [
            'untitled1' => "NIA: $alumno->nia - $alumno->fullName - $telefonoAlumne - $alumno->email",
            'untitled2' => config('contacto.nombre').' '.config('contacto.codi') ,
            'untitled3' => $grupo->Ciclo->vliteral,
            'untitled4' => "DNI: $tutor->dni - ".$tutor->fullName.' - '.$telefonoTutor.' - '.$tutor->email,
            'untitled18' => config('contacto.poblacion'),
            'untitled19' => day(Hoy()),
            'untitled20' => month(Hoy()),
            'untitled21' => substr(year(Hoy()), 2, 2),
            'untitled22' => $tutor->fullName,
        ];
    }
}
