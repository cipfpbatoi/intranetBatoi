<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuracio CRUD per a Task.
 */
final class TaskCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'descripcion',
        'vencimiento',
        'destino',
        'activa',
        'accio',
    ];

    /**
     * Configuracio de formulari create/edit.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'descripcion' => ['type' => 'text'],
        'vencimiento' => ['type' => 'date'],
        'fichero' => ['type' => 'file'],
        'enlace' => ['type' => 'text'],
        'destinatario' => ['type' => 'select'],
        'informativa' => ['type' => 'checkbox'],
        'activa' => ['type' => 'checkbox'],
        'action' => ['type' => 'select'],
    ];

    /**
     * Tipus de camp legacy per al model.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'informativa' => ['type' => 'checkbox'],
        'activa' => ['type' => 'checkbox'],
        'vencimiento' => ['type' => 'date'],
    ];

    /**
     * Regles de validacio.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'descripcion' => 'required|max:100',
        'vencimiento' => 'required|date',
        'destinatario' => 'required|numeric',
    ];
}

