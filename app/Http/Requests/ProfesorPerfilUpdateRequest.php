<?php

namespace Intranet\Http\Requests;

class ProfesorPerfilUpdateRequest extends AuthPerfilUpdateRequest
{
    /** Regles dels camps editables del perfil del professorat. */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'movil1' => 'nullable|max:14',
            'movil2' => 'nullable|max:14',
            'localitat' => 'nullable|string|max:100',
        ]);
    }
}
