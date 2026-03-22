<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Comision.
 *
 * Centralitza metadades de presentació (grid/form) i validació fora del model.
 */
final class ComisionCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'descripcion',
        'desde',
        'total',
        'situacion',
    ];

    /**
     * Configuració del formulari modal.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'idProfesor' => ['type' => 'hidden'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'fct' => ['type' => 'checkbox'],
        'servicio' => ['type' => 'textarea'],
        'alojamiento' => ['type' => 'text'],
        'comida' => ['type' => 'text'],
        'gastos' => ['type' => 'text'],
        'kilometraje' => ['type' => 'text'],
        'medio' => ['type' => 'select'],
        'marca' => ['type' => 'text'],
        'matricula' => ['type' => 'text'],
        'itinerario' => ['type' => 'textarea'],
    ];

    /**
     * Tipus de camp per a persistència/normalització en model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idProfesor' => ['type' => 'hidden'],
        'medio' => ['type' => 'select'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'fct' => ['type' => 'checkbox'],
        'servicio' => ['type' => 'textarea'],
    ];

    /**
     * Regles del formulari principal de comissió.
     *
     * @return array<string, string>
     */
    public static function requestRules(bool $isDirector): array
    {
        $day = $isDirector ? 'today' : 'tomorrow';

        return [
            'servicio' => 'required',
            'kilometraje' => 'required|integer',
            'desde' => "required|date|after:$day",
            'hasta' => 'required|date|after:desde',
            'alojamiento' => 'required|numeric',
            'comida' => 'required|numeric',
            'gastos' => 'required|numeric',
            'medio' => 'required|numeric',
            'marca' => 'required_if:medio,0|required_if:medio,1|max:30',
            'matricula' => 'required_if:medio,0|required_if:medio,1|max:10',
        ];
    }

    /**
     * Regles bàsiques per a l'API de comissions.
     *
     * @var array<string, string>
     */
    public const API_RULES = [
        'kilometraje' => 'integer',
        'profesor' => 'required',
        'servicio' => 'required',
        'entrada' => 'after:salida',
        'matricula' => 'required_with:marca',
    ];
}

