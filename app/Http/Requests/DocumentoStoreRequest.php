<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nota' => 'nullable|numeric|min:1|max:11',
        ];
    }
}
