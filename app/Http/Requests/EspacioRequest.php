<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Intranet\Entities\Espacio;

class EspacioRequest extends FormRequest
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
        $currentAula = $this->route('espacio');
        if ($currentAula instanceof Espacio) {
            $currentAula = $currentAula->getKey();
        }

        return [
            'aula' => [
                'required',
                'max:10',
                Rule::unique('espacios', 'aula')->ignore($currentAula, 'aula'),
            ],
            'descripcion' => 'required|max:100',
            'idDepartamento' => 'required',
        ];
    }

    /**
     * Missatges de validació específics del formulari d'espais.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aula.unique' => 'L\'aula ja existeix.',
        ];
    }
}
