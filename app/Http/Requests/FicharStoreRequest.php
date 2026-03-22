<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FicharStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigo' => 'required|string|max:25',
        ];
    }
}

