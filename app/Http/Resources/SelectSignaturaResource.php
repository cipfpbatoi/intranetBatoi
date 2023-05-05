<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SelectSignaturaResource extends JsonResource
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
            'texto' => $this->tipus.' '.$this->Centre.' '.$this->Alumne,
            'marked' => $this->sendTo == 0 ? 1 : 0
        ];
    }
}


