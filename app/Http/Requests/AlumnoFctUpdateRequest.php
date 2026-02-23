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
            'desde' => 'date',
            'hasta' => 'date',
            'horas' => 'required|numeric',
        ];
    }
}

