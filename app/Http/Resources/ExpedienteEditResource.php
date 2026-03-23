<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurs de lectura per al payload d'edició d'expedients.
 */
class ExpedienteEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal d'expedient.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'tipo',
            'idModulo',
            'idAlumno',
            'idProfesor',
            'explicacion',
            'fecha',
            'fechatramite',
        ];
    }
}
