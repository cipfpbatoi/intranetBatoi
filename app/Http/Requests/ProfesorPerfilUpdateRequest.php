<?php

namespace Intranet\Http\Requests;

class ProfesorPerfilUpdateRequest extends AuthPerfilUpdateRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'movil1' => 'nullable|max:14',
            'movil2' => 'nullable|max:14',
        ]);
    }
}

