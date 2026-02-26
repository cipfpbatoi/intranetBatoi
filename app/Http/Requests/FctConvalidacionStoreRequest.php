<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FctConvalidacionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idAlumno' => 'required',
            'asociacion' => 'required',
        ];
    }
}

