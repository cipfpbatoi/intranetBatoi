<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurs de lectura per al payload d'edició de comissions.
 */
class ComisionEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal de comissió.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'idProfesor',
            'desde',
            'hasta',
            'fct',
            'servicio',
            'alojamiento',
            'comida',
            'gastos',
            'kilometraje',
            'medio',
            'marca',
            'matricula',
            'itinerario',
        ];
    }
}
