<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Fct.
 *
 * Centralitza metadades de presentació (grid/form) fora del model Eloquent.
 */
final class FctCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'Centro',
        'Contacto',
        'Lalumnes',
        'Nalumnes',
        'sendCorreo',
    ];

    /**
     * Configuració de formulari per al CRUD de Fct.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'idInstructor' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'horas' => ['type' => 'text'],
        'autorizacion' => ['type' => 'checkbox'],
        'erasmus' => ['type' => 'checkbox'],
    ];
}

