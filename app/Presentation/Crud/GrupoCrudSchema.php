<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Grupo.
 *
 * Centralitza metadades de presentació (grid/form) fora del model Eloquent.
 */
final class GrupoCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'codigo',
        'nombre',
        'Xtutor',
        'Xciclo',
        'Torn',
    ];

    /**
     * Configuració de formulari per al CRUD de Grupo.
     *
     * Manté els mateixos camps que el model (`fillable`) i les personalitzacions
     * de UI que abans depenien de `inputTypes`.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'nombre' => ['type' => 'text'],
        'turno' => ['type' => 'text', 'disabled' => 'disabled'],
        'tutor' => ['type' => 'select'],
        'idCiclo' => ['type' => 'select'],
        'codigo' => ['type' => 'text'],
        'curso' => ['type' => 'text'],
    ];
}

