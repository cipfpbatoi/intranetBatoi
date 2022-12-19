<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
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
            'concierto' => $this->concierto,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'localidad' => $this->localidad,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'cif' => $this->cif,
            'actividad' => $this->actividad,
            'conveni' => $this->conveniNou
        ];
    }
}


