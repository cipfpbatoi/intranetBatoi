<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Incidencia.
 */
final class IncidenciaCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'Xestado',
        'DesCurta',
        'Xespacio',
        'XResponsable',
        'Xtipo',
        'fecha',
    ];

    /**
     * Configuració dels camps de formulari.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'tipo' => ['type' => 'select'],
        'espacio' => ['type' => 'select'],
        'material' => ['type' => 'select'],
        'descripcion' => ['type' => 'textarea'],
        'imagen' => ['type' => 'file'],
        'idProfesor' => ['type' => 'hidden'],
        'prioridad' => ['type' => 'select'],
        'observaciones' => ['type' => 'text'],
        'fecha' => ['type' => 'date'],
    ];

    /**
     * Tipus de camp utilitzats pel model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'fecha' => ['type' => 'date'],
        'imagen' => ['type' => 'file'],
    ];

    /**
     * Regles base de validació.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'descripcion' => 'required',
        'tipo' => 'required',
        'idProfesor' => 'required',
        'prioridad' => 'required',
        'observaciones' => 'max:255',
        'solucion' => 'max:255',
        'fecha' => 'date',
    ];

    /**
     * Regles de request afegint validació de fitxer d'imatge.
     *
     * @return array<string, string>
     */
    public static function requestRules(string $imagenRule): array
    {
        return array_merge(self::RULES, [
            'imagen' => $imagenRule,
        ]);
    }

    /**
     * Configuració de formulari d'edició.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function editFormFields(): array
    {
        return array_merge(self::FORM_FIELDS, [
            'espacio' => ['disabled' => 'disabled'],
            'material' => ['disabled' => 'disabled'],
        ]);
    }
}

