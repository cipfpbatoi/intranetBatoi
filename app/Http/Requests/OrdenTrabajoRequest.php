<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenTrabajoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'descripcion' => 'required',
            'tipo' => 'required',
        ];
    }
}
