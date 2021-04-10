<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DualResource extends JsonResource
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
            'idAlumno' => $this->idAlumno,
            'idColaboracion' => $this->Fct->idColaboracion,
            'idInstructor' => $this->Fct->idInstructor,
            'horas' => $this->horas,
            'beca' => $this->beca,
            'desde' => $this->desde,
            'hasta' => $this->hasta
        ];
    }
}


