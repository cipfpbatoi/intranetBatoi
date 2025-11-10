<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerfilFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,heic,heif|max:10240',
            'signatura' => 'nullable|file|mimes:jpg,jpeg,png,heic,heif|max:10240',
            'peu' => 'nullable|file|mimes:jpg,jpeg,png,heic,heif|max:10240',
        ];
    }
}
