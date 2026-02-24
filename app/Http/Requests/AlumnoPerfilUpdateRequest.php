<?php

namespace Intranet\Http\Requests;

class AlumnoPerfilUpdateRequest extends AuthPerfilUpdateRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'telef1' => 'nullable|max:14',
            'telef2' => 'nullable|max:14',
        ]);
    }
}

