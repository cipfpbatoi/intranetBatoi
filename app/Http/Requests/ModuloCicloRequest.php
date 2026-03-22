<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuloCicloRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idModulo' => 'required',
            'idCiclo' => 'required',
            'curso' => 'required',
        ];
    }
}
