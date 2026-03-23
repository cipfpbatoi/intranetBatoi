<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

/**
 * Recurs de lectura per al payload d'edició de tipus d'activitat.
 */
class TipoActividadEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal de tipus d'activitat.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'id',
            'cliteral',
            'vliteral',
            'justificacio',
        ];
    }
}
