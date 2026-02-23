<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Documento.
 */
final class DocumentoCrudSchema
{
    /**
     * Regles de validació del model legacy.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'tipoDocumento' => 'required',
        'descripcion' => 'required|max:200',
        'fichero' => 'sometimes|mimes:pdf,zip,odt,docx',
    ];

    /**
     * Tipus de camp per a persistència/normalització en model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'tipoDocumento' => ['type' => 'select'],
        'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
        'fichero' => ['type' => 'file'],
        'rol' => ['type' => 'hidden'],
        'propietario' => ['disabled' => 'disabled'],
        'grupo' => ['type' => 'select'],
        'supervisor' => ['type' => 'hidden'],
        'ciclo' => ['type' => 'hidden'],
        'detalle' => ['type' => 'textarea'],
        'activo' => ['type' => 'checkbox'],
    ];

    /**
     * Formulari base del CRUD de documents.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'tipoDocumento' => ['type' => 'select'],
        'rol' => ['type' => 'hidden'],
        'propietario' => ['disabled' => 'disabled'],
        'grupo' => ['type' => 'select'],
        'supervisor' => ['type' => 'hidden'],
        'ciclo' => ['type' => 'hidden'],
        'detalle' => ['type' => 'textarea'],
        'curso' => ['disabled' => 'disabled'],
        'descripcion' => ['type' => 'text'],
        'enlace' => ['type' => 'text'],
        'fichero' => ['type' => 'file'],
        'activo' => ['type' => 'checkbox'],
        'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
    ];

    /**
     * Formulari del flux de projecte.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function projectFormFields(): array
    {
        return [
            'tipoDocumento' => ['disabled' => 'disabled'],
            'propietario' => ['disabled' => 'disabled'],
            'curso' => ['disabled' => 'disabled'],
            'supervisor' => ['type' => 'hidden'],
            'ciclo' => ['type' => 'hidden'],
            'descripcion' => ['type' => 'text'],
            'detalle' => ['type' => 'textarea'],
            'nota' => ['type' => 'text'],
            'fichero' => ['type' => 'file'],
            'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
        ];
    }

    /**
     * Formulari del flux de qualitat.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function qualitatFormFields(): array
    {
        return [
            'tipoDocumento' => ['disabled' => 'disabled'],
            'instrucciones' => ['disabled' => 'disabled'],
            'curso' => ['disabled' => 'disabled'],
            'rol' => ['type' => 'hidden'],
            'propietario' => ['disabled' => 'disabled'],
            'grupo' => ['disabled' => 'disabled'],
            'supervisor' => ['type' => 'hidden'],
            'ciclo' => ['type' => 'hidden'],
            'detalle' => ['type' => 'textarea'],
            'descripcion' => ['type' => 'text'],
            'fichero' => ['type' => 'file'],
            'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
        ];
    }

    /**
     * Formulari d'edició segons siga enllaç o fitxer.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function editFormFields(bool $hasLink): array
    {
        $common = [
            'tipoDocumento' => ['type' => 'select'],
            'propietario' => ['disabled' => 'disabled'],
            'supervisor' => ['type' => 'hidden'],
            'rol' => ['type' => 'hidden'],
            'ciclo' => ['type' => 'hidden'],
            'detalle' => ['type' => 'textarea'],
            'curso' => ['disabled' => 'disabled'],
            'grupo' => ['disabled' => 'disabled'],
            'descripcion' => ['type' => 'text'],
            'activo' => ['type' => 'checkbox'],
            'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
        ];

        if ($hasLink) {
            $common['enlace'] = ['type' => 'text'];
            return $common;
        }

        $common['fichero'] = ['type' => 'file'];
        return $common;
    }
}

