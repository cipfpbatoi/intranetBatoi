<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la creació de FCT fictícies per convalidació, exempció o renúncia/no realitzada.
 */
class FctConvalidacionStoreRequest extends FormRequest
{
    /**
     * Qualsevol tutor autoritzat pel controlador pot enviar el formulari.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Regles de validació per al formulari de FCT fictícia.
     *
     * @return array<string, string>
     */
    public function rules()
    {
        return [
            'idAlumno' => 'required',
            'asociacion' => 'required',
            'calificacion' => 'required|integer|in:2,5',
        ];
    }
}
