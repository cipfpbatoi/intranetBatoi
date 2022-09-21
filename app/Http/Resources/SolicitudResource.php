<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'Alumne' => $this->Alumno->fullName??'',
            'Grupo' => $this->Alumno->Grupo->first()->nombre??'',
            'Email' => $this->Alumno->email,
            'Edat' => $this->Alumno->edat,
            'Professor que fa la petició' => $this->Profesor->fullName??'',
            'Motius de la sol·licitut' => $this->text1,
            'Aspectes afectats per la situació' => $this->text2??'',
            'Altres dades' => $this->text3??'',
            'Data' => $this->fecha,
            'Orientador' => $this->Orientador->fullName??'',
            'Estat' => $this->situacion,
            'Data Solució' => $this->fechaSolicion??'',
            'Solució' => $this->solucion,
        ];
    }
}


