<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Intranet\Entities\AlumnoFct;

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
        /** @var AlumnoFct $alumnoFct */
        $alumnoFct = $this->resource;
        $fct = $alumnoFct->Fct;

        return [
            'id' => $alumnoFct->id,
            'centro' => $fct?->relatedCenter()?->nombre,
            'nombre' => $alumnoFct->Alumno?->fullName,
            'a56' => $alumnoFct->a56,
            'desde' => $alumnoFct->desde,
            'hasta' => $alumnoFct->hasta,
            'pg0301' => $alumnoFct->pg0301,
        ];
    }
}

