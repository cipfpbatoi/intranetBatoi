<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlumnoFctResource extends JsonResource
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
            'idFct' => $this->idFct,
            'idAlumno' => $this->idAlumno,
            'calificacion' => $this->calificacion,
            'calProyecto' => $this->calProyecto,
            'a56' => $this->a56,
            'actas' => $this->actas,
            'beca' => $this->beca,
            'correoAlumno' => $this->correoAlumno,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'insercion' => $this->insercion,
            'pg0301' => $this->pg0301,
            'profesor' => $this->Alumno->Tutor[0]->dni,
            'alumno' => $this->quien,
            'horas' => $this->horas
        ];
    }
}


