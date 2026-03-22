<?php

namespace Intranet\Http\PrintResources;


use Intranet\Application\Grupo\GrupoService;

class ExempcioFCTResource extends PrintResource
{
    
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->file = 'SolicitudExencionFCT.pdf';
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
        $telefonoAlumne = ($alumno->telef1 != '')?$alumno->telef1:$alumno->telef2;
        $telefonoTutor = ($tutor->movil1 != '')?$tutor->movil1:$tutor->movil2;
        
        return [
            'Text1' => "$alumno->nia - $alumno->fullName",
            'Text2' => config('contacto.nombre').' '.config('contacto.codi') ,
            'Text3' => $grupo->Ciclo->vliteral,
            'Text4' => "$tutor->dni - ".$tutor->fullName,
            'untitled18' => config('contacto.poblacion'),
            'untitled19' => day(Hoy()),
            'untitled20' => month(Hoy()),
            'untitled21' => substr(year(Hoy()), 2, 2),
            'untitled22' => $tutor->fullName,
        ];
    }
}
