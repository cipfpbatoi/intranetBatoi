<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MyMailStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'collect' => [
                'required',
                'string',
                Rule::in(array_keys((array) config('auxiliares.collectMailable', []))),
            ],
        ];
    }
}

