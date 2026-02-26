<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportStoreRequest extends FormRequest
{
    public function authorize()
    {
        return userIsAllow(config('roles.rol.administrador'));
    }

    public function rules()
    {
        return [
            'fichero' => 'required|file|mimes:xml',
            'primera' => 'nullable|in:on,off,1,0,true,false',
            'mode' => 'nullable|in:full,create_only',
        ];
    }
}
