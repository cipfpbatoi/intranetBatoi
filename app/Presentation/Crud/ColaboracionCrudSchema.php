<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Colaboracion.
 *
 * Centralitza metadades de presentació (grid/form) fora del model Eloquent.
 */
final class ColaboracionCrudSchema
{
    /**
     * Regles de validació de negoci per al model legacy.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'idCentro' => 'required|composite_unique:colaboraciones,idCentro,idCiclo',
        'idCiclo' => 'required',
        'email' => 'email',
        'puestos' => 'required|numeric',
        'telefono' => 'max:20',
    ];

    /**
     * Regles del formulari manual de canvi d'estat/contacte.
     *
     * @var array<string, string>
     */
    public const UPDATE_RULES = [
        'contacto' => 'required',
        'telefono' => 'required',
        'email' => 'required|email',
        'puestos' => 'required|numeric',
        'estado' => 'required',
    ];

    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'short',
        'Xciclo',
        'puestos',
        'Xestado',
        'localidad',
        'contacto',
        'email',
        'telefono',
        'horari',
        'profesor',
        'ultimo',
        'anotacio',
    ];

    /**
     * Configuració del formulari modal.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'idCentro' => ['type' => 'hidden'],
        'idCiclo' => ['type' => 'hidden'],
        'contacto' => ['type' => 'text'],
        'telefono' => ['type' => 'number'],
        'email' => ['type' => 'email'],
        'puestos' => ['type' => 'text'],
        'estado' => ['type' => 'select'],
        'anotacio' => ['type' => 'textarea'],
    ];

    /**
     * Tipus de camp per a persistència/normalització en model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idCentro' => ['type' => 'hidden'],
        'idCiclo' => ['type' => 'hidden'],
        'telefono' => ['type' => 'number'],
        'email' => ['type' => 'email'],
        'tutor' => ['type' => 'hidden'],
    ];
}
