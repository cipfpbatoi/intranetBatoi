<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuracio CRUD per a TipoActividad.
 */
final class TipoActividadCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'departamento',
        'vliteral',
    ];

    /**
     * Configuracio de camps de formulari.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'id' => ['type' => 'hidden'],
        'cliteral' => ['type' => 'text'],
        'vliteral' => ['type' => 'text'],
        'justificacio' => ['type' => 'textarea'],
    ];

    /**
     * Tipus de camp legacy del model.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'justificacio' => ['type' => 'textarea'],
    ];

    /**
     * Regles de validacio.
     *
     * @var array<string, array<int, string|\Illuminate\Validation\Rules\Exists>>
     */
    public const RULES = [
        'cliteral' => ['required', 'string', 'max:50'],
        'vliteral' => ['required', 'string', 'max:50'],
        'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],
    ];
}

