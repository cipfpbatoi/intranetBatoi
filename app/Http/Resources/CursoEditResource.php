<?php

declare(strict_types=1);

namespace Intranet\Http\Resources;

/**
 * Recurs de lectura per al payload d'edició de cursos.
 */
class CursoEditResource extends ModelEditResource
{
    /**
     * Camps exposats pel modal de curs.
     *
     * @return array<int, string>
     */
    protected function fields(): array
    {
        return [
            'titulo',
            'tipo',
            'comentarios',
            'profesorado',
            'activo',
            'horas',
            'fecha_inicio',
            'fecha_fin',
            'hora_ini',
            'hora_fin',
            'aforo',
        ];
    }
}
