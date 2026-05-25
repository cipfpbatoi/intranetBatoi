<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validacio del formulari de valoracio d'activitats.
 */
class ValoracionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'desenvolupament' => 'required|string',
            'valoracio' => 'required|string',
            'aspectes' => 'required|string',
            'dades' => 'required|string',
        ];
    }

    /**
     * Missatges personalitzats per a la validacio.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'required' => 'Cal completar tots els camps de la valoracio.',
        ];
    }

    /**
     * Noms de camps per mostrar en errors de validacio.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'desenvolupament' => 'desenvolupament',
            'valoracio' => 'valoracio pedagogica',
            'aspectes' => 'aspectes transversals',
            'dades' => 'altres dades',
        ];
    }
}
