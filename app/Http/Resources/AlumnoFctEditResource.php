<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurs de lectura per al payload d'edició de FCT d'alumnat.
 */
class AlumnoFctEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal d'FCT d'alumnat.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'id',
            'desde',
            'hasta',
            'beca',
            'autorizacion',
            'flexible',
            'valoracio',
        ];
    }
}
