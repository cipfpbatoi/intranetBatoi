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
            'id' => $this->id,
            'Alumne' => $this->Alumno->fullName??'',
            'Professor' => $this->Profesor->fullName??'',
            'Motius' => $this->text1,
            'Aspectes' => $this->text2??'',
            'Altes' => $this->text3??'',
            'Data' => $this->fecha,
            'Data Solució' => $this->fechaSolicion??'',
            'Orientador' => $this->Orientador->fullName??'',
            'Estat' => $this->situacion,
        ];
    }
}


