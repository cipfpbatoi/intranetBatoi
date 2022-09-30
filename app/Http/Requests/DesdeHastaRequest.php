<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesdeHastaRequest extends FormRequest
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
            'desde' => 'required|date',
            'hasta' => 'required|date|after:desde',
        ];
    }
}
