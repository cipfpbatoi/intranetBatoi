<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignaturaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipus' => 'required',
            'fct' => 'required',
            'file' => 'required|file|mimes:pdf|max:2048',
        ];
    }
}

