<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Instructor.
 */
final class InstructorCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'dni',
        'nombre',
        'departamento',
        'Nfcts',
        'Xcentros',
        'email',
        'telefono',
    ];

    /**
     * Configuració dels camps de formulari.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'dni' => ['type' => 'card'],
        'name' => ['type' => 'name'],
        'surnames' => ['type' => 'name'],
        'email' => ['type' => 'email'],
        'telefono' => ['type' => 'number'],
    ];

    /**
     * Tipus de camp utilitzats pel model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = self::FORM_FIELDS;

    /**
     * Regles de validació.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'dni' => 'required|max:12',
        'name' => 'required|max:60',
        'surnames' => 'required|max:60',
        'email' => 'required|email|max:60',
        'telefono' => 'max:20',
    ];
}

