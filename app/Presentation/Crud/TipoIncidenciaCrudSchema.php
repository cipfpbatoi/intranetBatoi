<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

use Illuminate\Validation\Rule;

/**
 * Esquema de configuracio CRUD per a TipoIncidencia.
 */
final class TipoIncidenciaCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'nombre',
        'nom',
        'profesor',
        'tipo',
    ];

    /**
     * Configuracio dels camps de formulari.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'id' => ['type' => 'text'],
        'nombre' => ['type' => 'text'],
        'nom' => ['type' => 'text'],
        'idProfesor' => ['type' => 'select'],
        'tipus' => ['type' => 'select'],
    ];

    /**
     * Tipus de camp legacy del model.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idProfesor' => ['type' => 'select'],
        'tipus' => ['type' => 'select'],
    ];

    /**
     * Regles de validacio per a create/update.
     *
     * @return array<string, mixed>
     */
    public static function requestRules(int|string|null $currentId = null): array
    {
        return [
            'id' => [
                'required',
                'numeric',
                'max:99',
                Rule::unique('tipoincidencias', 'id')->ignore($currentId, 'id'),
            ],
            'nombre' => 'required|max:40',
            'nom' => 'required|max:40',
            'idProfesor' => 'required|exists:profesores,dni',
            'tipus' => 'required',
        ];
    }
}

