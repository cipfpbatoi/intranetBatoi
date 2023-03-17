<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartamentoRequest extends FormRequest
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
            'id' => 'required|numeric|max:999,unique:departamentos,id',
            'cliteral' => 'required|max:30',
            'vliteral' => 'required|max:30',
            'depcurt' => 'required|max:3',
            'idProfesor' => 'required|exists:profesores,dni'

        ];
    }
}
