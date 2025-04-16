<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SelectFctResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $nom_centre = isset($this->Colaboracion->Centro->nombre) ?
            $this->Colaboracion->Centro->nombre:
            $this->Fct->Colaboracion->Centro->nombre;
        $instructor = isset($this->Instructor->nombre) ?
            $this->Instructor->nombre:
            $this->Fct->Instructor->nombre;
        return [
            'id' => $this->id,
            'texto' => "$instructor($nom_centre)",
            'marked' => $this->marked
        ];
    }
}


