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
        $material = $this->Material;
        $procedencies = config('auxiliares.procedenciaMaterial');
        $estats = config('auxiliares.estadoMaterial');

        $procedencia = $material?->procedencia;
        $estat = $material?->estado;

        return [
                'id' => $this->idMaterial,
                'numSèrie' => $material?->numSerie ?? $material?->nserieprov ?? '',
                'descripció' => $material?->descripcion ?? '',
                'marca' => $material?->marca ?? '',
                'model' => $material?->modelo ?? '',
                'procedència' => is_numeric($procedencia) && array_key_exists((int) $procedencia, $procedencies)
                    ? $procedencies[(int) $procedencia]
                    : '',
                'estat' => is_numeric($estat) && array_key_exists((int) $estat, $estats)
                    ? $estats[(int) $estat]
                    : '',
                'espai' => $material?->espacio ?? '',
                'lot_article' => $material?->articulo_lote_id ?? null,
                'registre' => $material?->LoteArticulo?->lote_id ?? '',
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

