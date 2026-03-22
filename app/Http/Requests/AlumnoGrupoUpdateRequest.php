<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Entities\AlumnoGrupo;

class AlumnoGrupoUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return (new AlumnoGrupo())->getRules();
    }
}
