<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuloRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cliteral' => 'required',
            'vliteral' => 'required',
        ];
    }
}
