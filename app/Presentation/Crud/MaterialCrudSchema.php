<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Material.
 */
final class MaterialCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'descripcion',
        'Estado',
        'espacio',
        'unidades',
    ];

    /**
     * Configuració de formulari de create/edit.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'nserieprov' => ['type' => 'text'],
        'descripcion' => ['type' => 'text'],
        'marca' => ['type' => 'text'],
        'modelo' => ['type' => 'text'],
        'ISBN' => ['type' => 'text'],
        'espacio' => ['type' => 'select'],
        'procedencia' => ['type' => 'select'],
        'proveedor' => ['type' => 'text'],
        'estado' => ['type' => 'select'],
        'inventariable' => ['type' => 'checkbox'],
        'unidades' => ['type' => 'number'],
        'articulo_lote_id' => ['type' => 'hidden'],
    ];

    /**
     * Tipus de camp utilitzats pel model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'espacio' => ['type' => 'select'],
        'procedencia' => ['type' => 'select'],
        'inventariable' => ['type' => 'checkbox'],
        'estado' => ['type' => 'select'],
        'articulo_lote_id' => ['type' => 'hidden'],
    ];

    /**
     * Regles de validació.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'descripcion' => 'required',
        'espacio' => 'required',
        'unidades' => 'numeric',
    ];
}
