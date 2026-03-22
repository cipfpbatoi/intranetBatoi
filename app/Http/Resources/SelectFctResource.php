<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Intranet\Entities\Fct;

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
        /** @var Fct|null $fct */
        $fct = $this->resource instanceof Fct ? $this->resource : $this->Fct;
        $nom_centre = $fct?->relatedCenter()?->nombre;
        $instructor = $this->Instructor->nombre ?? $fct?->Instructor?->nombre;

        return [
            'id' => $this->id,
            'texto' => trim((string) $instructor) . '(' . trim((string) $nom_centre) . ')',
            'marked' => $this->marked
        ];
    }
}

