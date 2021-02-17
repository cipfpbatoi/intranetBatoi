<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return ['registre' => $this->registre,
            'procedencia' => $this->procedencia,
            'proveedor' => $this->proveedor,
            'fechaAlta' => $this->fechaAlta,
            'estado' => $this->estado
        ];
    }
}


