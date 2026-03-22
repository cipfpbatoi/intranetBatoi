<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CentroRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idEmpresa' => 'required',
            'nombre' => 'required',
            'direccion' => 'required',
            'localidad' => 'required',
        ];
    }
}
