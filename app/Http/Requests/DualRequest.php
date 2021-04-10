<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DualRequest extends FormRequest
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
            'idAlumno' => 'sometimes|required',
            'idColaboracion' => 'sometimes|required',
            'idInstructor' => 'sometimes|required',
            'desde' => 'sometimes|required|date',
            'hasta' => 'sometimes|required|date',
            'beca' => 'numeric'
        ];
    }
}
