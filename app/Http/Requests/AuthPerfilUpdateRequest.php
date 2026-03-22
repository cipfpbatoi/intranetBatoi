<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthPerfilUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:45',
            'emailItaca' => 'nullable|email|max:45',
            'telef1' => 'nullable|max:14',
            'telef2' => 'nullable|max:14',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,heic,heif|max:10240',
        ];
    }
}
