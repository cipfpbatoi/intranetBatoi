<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Entities\Profesor;

class ProfesorUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return (new Profesor())->getRules();
    }
}
