<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Concerns\BatoiModels;
use Intranet\Entities\Ciclo;
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

    protected $fillable = ['question', 'scala', 'ppoll_id', 'choices', 'idCiclo'];
    protected $rules = [
        'ppoll_id' => 'required',
        'question' => 'required',
        'scala' => 'nullable|numeric|between:0,10',
        'choices' => 'nullable|string',
        'idCiclo' => 'nullable|exists:ciclos,id',
    ];
    protected $inputTypes = [
        'ppoll_id' => ['disabled' => 'disabled'],
        'choices' => ['type' => 'textarea'],
        'idCiclo' => ['type' => 'select'],
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
     * Retorna el cicle destinatari de la pregunta, si està restringida.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Ciclo()
    {
        return $this->belongsTo(Ciclo::class, 'idCiclo', 'id');
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
     * Determina si la pregunta aplica al cicle indicat.
     */
    public function matchesCycle(?int $cycleId): bool
    {
        return $this->idCiclo === null || (int) $this->idCiclo === $cycleId;
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
     * Retorna una etiqueta amigable del cicle destinatari.
     */
    public function getTargetCycleLabelAttribute(): string
    {
        if ($this->idCiclo === null) {
            return 'Tots els cicles';
        }

        return $this->Ciclo?->ciclo ?? 'Cicle ' . $this->idCiclo;
    }

    /**
     * Retorna l'enunciat amb el cicle afegit quan la pregunta és específica.
     */
    public function getQuestionLabelAttribute(): string
    {
        if ($this->idCiclo === null) {
            return (string) $this->question;
        }

        return sprintf('%s [%s]', $this->question, $this->target_cycle_label);
    }

    /**
     * Retorna les opcions disponibles per al selector de cicle.
     *
     * @return array<int|string, string>
     */
    public function getIdCicloOptions(): array
    {
        return ['' => 'Tots els cicles'] + hazArray(Ciclo::orderBy('ciclo')->get(), 'id', 'ciclo');
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

        $lines = $this->splitChoices((string) $choices);
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

    /**
     * Separa les opcions de selecció admetent formats antics.
     *
     * A banda de salts de línia, admet valors separats per `|`
     * quan tot ve en una sola línia.
     *
     * @return array<int, string>
     */
    private function splitChoices(string $choices): array
    {
        $lines = preg_split('/\r\n|\r|\n|\|/', $choices) ?: [];

        return $lines;
    }
}
