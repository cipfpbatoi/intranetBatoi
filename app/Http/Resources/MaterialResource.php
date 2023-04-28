<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
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
            'articulo' => $this->LoteArticulo->Articulo->descripcion,
            'descripcion' => $this->descripcion($this->descripcion, $this->modelo, $this->marca),
            'estado' => config('auxiliares.estadoMaterial')[$this->estado],
            'espacio' => $this->espacio,
        ];
    }

    private function descripcion($des, $mod, $mar)
    {
        $descripcion = $des;
        if (isset($mod)){
            $descripcion .= " ($mod)";
        }
        if (isset($mar)){
            $descripcion .= " - $mar";
        }
        return $descripcion;
    }
}


