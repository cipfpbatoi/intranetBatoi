<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a AlumnoFctAval.
 */
final class AlumnoFctAvalCrudSchema
{
    /**
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'Nombre',
        'Qualificacio',
        'Projecte',
        'hasta',
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'id' => ['type' => 'hidden'],
        'idAlumno' => ['type' => 'hidden'],
        'idFct' => ['type' => 'hidden'],
        'calificacion' => ['type' => 'hidden'],
        'calProyecto' => ['type' => 'text'],
    ];
}

