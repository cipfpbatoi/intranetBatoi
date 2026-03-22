<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a AlumnoFct.
 *
 * Centralitza metadades de presentació (grid/form) fora del model Eloquent.
 */
final class AlumnoFctCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'NomEdat',
        'Centro',
        'Instructor',
        'desde',
        'horasRealizadas',
        'hasta',
        'finPracticas',
    ];

    /**
     * Configuració de formulari modal per a AlumnoFct.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'id' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'beca' => ['type' => 'hidden'],
        'autorizacion' => ['type' => 'checkbox'],
        'flexible' => ['type' => 'checkbox'],
        'valoracio' => ['type' => 'textarea'],
    ];
}

