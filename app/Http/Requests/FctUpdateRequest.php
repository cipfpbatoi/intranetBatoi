<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FctUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idInstructor' => 'required',
        ];
    }
}
