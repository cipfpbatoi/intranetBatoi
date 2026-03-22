<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendAvaluacioEmailStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nia' => 'required|string|max:12',
        ];
    }
}

