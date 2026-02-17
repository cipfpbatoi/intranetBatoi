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

    /**
     * Tipus d'input per a persistència/normalització en model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'idInstructor' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'autorizacion' => ['type' => 'checkbox'],
    ];

    /**
     * Regles de validació de FCT.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'idAlumno' => 'sometimes|required',
        'idColaboracion' => 'sometimes|required',
        'idInstructor' => 'sometimes|required',
        'desde' => 'sometimes|required|date',
        'hasta' => 'sometimes|required|date',
    ];
}
