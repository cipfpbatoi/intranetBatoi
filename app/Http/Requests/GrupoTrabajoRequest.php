<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrupoTrabajoRequest extends FormRequest
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
            'literal' => 'required|max:40',
            'objetivos' => 'required',
        ];
    }
}
