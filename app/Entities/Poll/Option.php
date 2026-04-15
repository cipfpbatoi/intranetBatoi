<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Concerns\BatoiModels;
use Illuminate\Database\Eloquent\Model;

/**
 * Pregunta d'una plantilla d'enquesta.
 *
 * Manté compatibilitat amb els tipus històrics:
 * - numèric: `scala` major que zero,
 * - text lliure: `scala` igual a zero i sense `choices`,
 * - selecció: `choices` amb una opció per línia.
 */
class Option extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $fillable = ['question', 'scala', 'ppoll_id', 'choices'];
    protected $rules = [
        'ppoll_id' => 'required',
        'question' => 'required',
        'scala' => 'nullable|numeric|between:0,10',
        'choices' => 'nullable|string',
    ];
    protected $inputTypes = [
        'ppoll_id' => ['disabled' => 'disabled'],
        'choices' => ['type' => 'textarea'],
    ];
    public $timestamps = false;

    /**
     * An option belongs to one poll
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Check if the option is Closed
     *
     * @return bool
     */
    public function isPollClosed()
    {
        return $this->poll->activo;
    }

    /**
     * Indica si la pregunta és de valoració numèrica.
     */
    public function isNumericType(): bool
    {
        return !$this->isSelectType() && (int) $this->scala > 0;
    }

    /**
     * Indica si la pregunta és de text lliure.
     */
    public function isTextType(): bool
    {
        return !$this->isNumericType() && !$this->isSelectType();
    }

    /**
     * Indica si la pregunta ofereix una llista tancada d'opcions.
     */
    public function isSelectType(): bool
    {
        return count($this->choice_values) > 0;
    }

    /**
     * Retorna el tipus lògic de la pregunta.
     */
    public function getKindAttribute(): string
    {
        if ($this->isSelectType()) {
            return 'select';
        }

        if ($this->isNumericType()) {
            return 'numeric';
        }

        return 'text';
    }

    /**
     * Retorna una etiqueta amigable del tipus de resposta.
     */
    public function getDisplayTypeAttribute(): string
    {
        return match ($this->kind) {
            'numeric' => 'Numèrica',
            'select' => 'Selecció',
            default => 'Text lliure',
        };
    }

    /**
     * Normalitza les opcions configurades com una llista neta.
     *
     * @return array<int, string>
     */
    public function getChoiceValuesAttribute(): array
    {
        $choices = $this->attributes['choices'] ?? null;
        if (!$choices) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $choices) ?: [];
        $clean = [];

        foreach ($lines as $line) {
            $value = trim($line);
            if ($value === '' || in_array($value, $clean, true)) {
                continue;
            }

            $clean[] = $value;
        }

        return $clean;
    }
}
