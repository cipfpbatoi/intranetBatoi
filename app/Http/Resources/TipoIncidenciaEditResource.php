<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

/**
 * Recurs de lectura per al payload d'edició de tipus d'incidència.
 */
class TipoIncidenciaEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal de tipus d'incidència.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'id',
            'nombre',
            'nom',
            'idProfesor',
            'tipus',
        ];
    }
}
