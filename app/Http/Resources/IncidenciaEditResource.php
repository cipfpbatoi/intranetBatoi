<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

/**
 * Recurs de lectura per al payload d'edició d'incidències.
 */
class IncidenciaEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal d'incidència.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'tipo',
            'espacio',
            'material',
            'descripcion',
            'imagen',
            'idProfesor',
            'prioridad',
            'observaciones',
            'fecha',
        ];
    }
}
