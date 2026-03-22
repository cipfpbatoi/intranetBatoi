<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Curso.
 */
final class CursoCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'titulo',
        'estado',
        'fecha_inicio',
        'NAlumnos',
    ];

    /**
     * Configuració de formulari modal.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'titulo' => ['type' => 'text'],
        'tipo' => ['type' => 'hidden'],
        'comentarios' => ['type' => 'textarea'],
        'profesorado' => ['type' => 'textarea'],
        'activo' => ['type' => 'radios', 'default' => [1 => 'Activo', 0 => 'Inactivo'], 'inline' => 'inline'],
        'horas' => ['type' => 'text'],
        'fecha_inicio' => ['type' => 'date'],
        'fecha_fin' => ['type' => 'date'],
        'hora_ini' => ['type' => 'time'],
        'hora_fin' => ['type' => 'time'],
        'aforo' => ['type' => 'text'],
    ];

    /**
     * Tipus de camp per a persistència/normalització en model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'tipo' => ['type' => 'hidden'],
        'comentarios' => ['type' => 'textarea'],
        'profesorado' => ['type' => 'textarea'],
        'activo' => ['type' => 'radios', 'default' => [1 => 'Activo', 0 => 'Inactivo'], 'inline' => 'inline'],
        'fecha_inicio' => ['type' => 'date'],
        'fecha_fin' => ['type' => 'date'],
        'hora_ini' => ['type' => 'time'],
        'hora_fin' => ['type' => 'time'],
    ];

    /**
     * Regles de validació del formulari de curs.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'titulo' => 'required',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date',
        'horas' => 'required|integer|max:255',
        'aforo' => 'numeric',
        'comentarios' => 'required',
    ];
}

