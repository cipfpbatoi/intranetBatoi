<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PPollRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'what' => 'required',
        ];
    }
}
