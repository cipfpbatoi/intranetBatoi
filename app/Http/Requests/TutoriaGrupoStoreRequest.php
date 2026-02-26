<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\TutoriaGrupoCrudSchema;

class TutoriaGrupoStoreRequest extends FormRequest
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
