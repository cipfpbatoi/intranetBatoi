<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurs de lectura per al payload d'edició d'activitats.
 */
class ActividadEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal d'activitat.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'id',
            'tipo_actividad_id',
            'name',
            'desde',
            'hasta',
            'poll',
            'tipus_activitat',
            'ubicacio_activitat',
            'complementaria',
            'fueraCentro',
            'transport',
            'descripcion',
            'objetivos',
            'extraescolar',
            'comentarios',
            'recomanada',
        ];
    }
}
