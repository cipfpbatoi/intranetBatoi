<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\ColaboracionCrudSchema;

class ColaboracionRequest extends FormRequest
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
        return ColaboracionCrudSchema::UPDATE_RULES;
    }

    /**
     * Missatges curts per al formulari d'edició de col·laboració.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'contacto.required' => 'El contacte és obligatori.',
            'telefono.required' => 'El telèfon és obligatori.',
            'email.required' => 'L\'email és obligatori.',
            'email.email' => 'L\'email no té un format vàlid.',
            'puestos.required' => 'Els llocs són obligatoris.',
            'puestos.numeric' => 'Els llocs han de ser numèrics.',
            'estado.required' => 'L\'estat és obligatori.',
        ];
    }
}
