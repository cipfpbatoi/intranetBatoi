<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IpGuardiaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ip' => 'required',
            'codOcup' => 'required',
        ];
    }
}
