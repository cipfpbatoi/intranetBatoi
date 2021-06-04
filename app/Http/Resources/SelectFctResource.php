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
        return [
            'id' => $this->id,
            'texto' => $this->Instructor->nombre.'('.$this->Colaboracion->Centro->nombre.') Periode: '.$this->periode,
            'marked' => $this->marked
        ];
    }
}


