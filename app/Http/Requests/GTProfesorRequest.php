<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GTProfesorRequest extends FormRequest
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
            'idGrupoTrabajo' => 'required|exists:grupos_trabajo,id',
            'idProfesor' => [
                'required',
                'exists:profesores,dni',
                Rule::unique('miembros')->where(function ($query) {
                    return $query
                        ->where('idGrupoTrabajo', $this->input('idGrupoTrabajo'))
                        ->where('idProfesor', $this->input('idProfesor'));
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'idProfesor.unique' => 'Eixa Persona ja pertany a eixe grup.',
        ];
    }
}
