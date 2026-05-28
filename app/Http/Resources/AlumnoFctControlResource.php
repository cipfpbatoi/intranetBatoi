<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlumnoFctControlResource extends JsonResource
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
            'centro' => $this->Fct->Colaboracion->Centro->nombre,
            'nombre' => $this->Alumno->fullName,
            'a56' => $this->a56,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'pg0301' => $this->pg0301,
        ];
    }
}


