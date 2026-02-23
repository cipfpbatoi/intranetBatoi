<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuracio CRUD per a Tutoria.
 */
final class TutoriaCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'descripcion',
        'tipos',
        'hasta',
        'Xobligatoria',
        'Grupo',
        'feedBack',
    ];

    /**
     * Tipus de camp legacy del model.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'obligatoria' => ['type' => 'checkbox'],
        'fichero' => ['type' => 'file'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'grupos' => ['type' => 'select'],
        'tipo' => ['type' => 'select'],
    ];

    /**
     * Regles de validacio.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'desde' => 'required|date',
        'hasta' => 'required|date',
        'tipo' => 'required',
        'descripcion' => 'required',
        'grupos' => 'required',
    ];
}

