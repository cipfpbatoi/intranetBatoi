<?php

namespace Intranet\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\TaskCrudSchema;

/**
 * Validació del formulari de manteniment de tasques.
 */
class TaskRequest extends FormRequest
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
        return TaskCrudSchema::RULES;
    }

    /**
     * Normalitza la data del datepicker legacy abans d'aplicar la regla `date`.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'vencimiento' => $this->normalizeDate($this->input('vencimiento')),
        ]);
    }

    /**
     * Admet formats legacy i HTML5 per a la data de venciment.
     *
     * @param mixed $value
     * @return mixed
     */
    private function normalizeDate($value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $formats = [
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
        ];

        $value = trim((string) $value);

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            $errors = \DateTime::getLastErrors();
            $hasErrors = is_array($errors)
                && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0);

            if ($date !== false && !$hasErrors) {
                return Carbon::instance($date)->format('Y-m-d');
            }
        }

        return $value;
    }
}
