<?php

namespace Intranet\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\ComisionCrudSchema;

/**
 * Validació del formulari de comissions.
 *
 * Normalitza les dates del datepicker legacy abans de llançar les regles de
 * Laravel, perquè el formulari envia `dd/mm/yyyy HH:mm` i el validador `date`
 * no sempre l'interpreta bé segons locale/entorn.
 */
class ComisionRequest extends FormRequest
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
        $isDirector = authUser()->dni == config('avisos.director');

        return ComisionCrudSchema::requestRules($isDirector);
    }

    /**
     * Normalitza les dates del formulari abans de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'desde' => $this->normalizeDateTime($this->input('desde')),
            'hasta' => $this->normalizeDateTime($this->input('hasta')),
        ]);
    }

    /**
     * Admet formats legacy i HTML5 per al camp datetime.
     */
    private function normalizeDateTime($value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $formats = [
            'd/m/Y H:i',
            'd/m/Y G:i',
            'm/d/Y H:i',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d\TH:i',
        ];

        $value = trim((string) $value);

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            $errors = \DateTime::getLastErrors();
            $hasErrors = is_array($errors) && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0);

            if ($date !== false && !$hasErrors) {
                return Carbon::instance($date)->format('Y-m-d H:i:s');
            }
        }

        return $value;
    }
}
