<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

/**
 * Recurs de lectura per al payload d'edició de tasques.
 */
class TaskEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal de task.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'descripcion',
            'vencimiento',
            'fichero',
            'enlace',
            'destinatario',
            'informativa',
            'activa',
            'action',
        ];
    }
}
