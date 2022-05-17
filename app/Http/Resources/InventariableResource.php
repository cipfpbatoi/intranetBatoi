<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventariableResource extends JsonResource
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
            'nserieprov' => $this->nserieprov??'',
            'marca' => $this->marca??'',
            'modelo' => $this->modelo??'',
            'espacio' => $this->espacio,
            'descripcion' => $this->descripcio,

        ];
    }
}


