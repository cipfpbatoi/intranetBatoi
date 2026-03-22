<?php

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $alumno = $this->Alumno;
        $grupo = $alumno?->Grupo?->first();

        return [
            'Alumne' => $alumno->fullName ?? '',
            'Grupo' => $grupo->nombre ?? '',
            'Email' => $alumno->email ?? '',
            'Edat' => $alumno->edat ?? '',
            'Professor que fa la petició' => $this->Profesor->fullName??'',
            'Motius de la sol·licitut' => $this->text1,
            'Aspectes afectats per la situació' => $this->text2??'',
            'Altres dades' => $this->text3??'',
            'Data' => $this->fecha,
            'Orientador' => $this->Orientador->fullName??'',
            'Estat' => $this->situacion,
            'Data Solució' => $this->fechaSolucion ?? '',
            'Solució' => $this->solucion,
        ];
    }
}

