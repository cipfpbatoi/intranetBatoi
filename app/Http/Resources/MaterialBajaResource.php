<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialBajaResource extends JsonResource
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
                'id' => $this->idMaterial,
                'numSèrie' => $this->Material->numSerie,
                'descripció' => $this->Material->descripcion,
                'marca' => $this->Material->marca,
                'model' => $this->Material->modelo,
                'procedència' => config('auxiliares.procedenciaMaterial')[$this->Material->procedencia],
                'estat' => config('auxiliares.estadoMaterial')[$this->Material->estado],
                'espai' => $this->Material->espacio,
                'lot_article' => $this->Material->articulo_lote_id,
                'registre' => $this->Material->LoteArticulo->lote_id,
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


