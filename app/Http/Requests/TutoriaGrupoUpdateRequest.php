<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\TutoriaGrupoCrudSchema;

class TutoriaGrupoUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return TutoriaGrupoCrudSchema::RULES;
    }
}
