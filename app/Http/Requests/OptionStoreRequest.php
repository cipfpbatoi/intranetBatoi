<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validació de creació de preguntes d'una plantilla d'enquesta.
 */
class OptionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ppoll_id' => 'required',
            'question' => 'required',
            'kind' => 'required|in:numeric,text,select',
            'scala' => 'required_if:kind,numeric|nullable|numeric|between:1,10',
            'choices' => 'required_if:kind,select|nullable|string',
        ];
    }
}
