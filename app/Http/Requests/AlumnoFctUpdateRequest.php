<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlumnoFctUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'desde' => 'required|date',
            'hasta' => 'required|date|after:desde',
        ];
    }
}
