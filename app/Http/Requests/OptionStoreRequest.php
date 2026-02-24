<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OptionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ppoll_id' => 'required',
            'question' => 'required',
            'scala' => 'numeric|between:0,10',
        ];
    }
}

