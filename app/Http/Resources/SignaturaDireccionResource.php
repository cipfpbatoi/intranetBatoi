<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SignaturaDireccionResource extends JsonResource
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
            'texto' => $this->tipus.' -> '.$this->centre.' de '.$this->profesor. ' per '.$this->alumne,
            'marked' => 1
        ];
    }
}


