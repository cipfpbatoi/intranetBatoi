<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticuloLoteResource extends JsonResource
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
            'descripcion' => $this->articulo->descripcion,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'unidades' => $this->unidades,
        ];
    }
}


