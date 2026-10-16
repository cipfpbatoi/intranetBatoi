<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Vehicle associat a un professor i autoritzat per a accedir a l'aparcament.
 */
class Cotxe extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    /**
     * Camps assignables en persistència massiva/controlada.
     *
     * @var array<int, string>
     */
    protected $fillable = ['matricula', 'marca', 'idProfesor'];

    /**
     * Normalitza una matrícula al format canònic usat en persistència i cerca.
     */
    public static function normalizeMatricula(mixed $matricula): string
    {
        if (!is_scalar($matricula) && !($matricula instanceof \Stringable)) {
            return '';
        }

        $withoutSeparators = preg_replace('/[\s-]+/u', '', trim((string) $matricula));

        return Str::upper((string) $withoutSeparators);
    }

    /**
     * Impedix que una matrícula es persistisca fora del format canònic.
     */
    public function setMatriculaAttribute(mixed $matricula): void
    {
        $this->attributes['matricula'] = self::normalizeMatricula($matricula);
    }

    /**
     * Professor propietari del vehicle.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    /**
     * Limita la consulta a matrícules que diferixen en un únic caràcter.
     */
    public function scopePlateHamming1($query, string $matricula)
    {
        $plate = self::normalizeMatricula($matricula);
        $len   = mb_strlen($plate);

        // Patrons LIKE amb un únic _ a cada posició
        $patterns = [];
        for ($i = 0; $i < $len; $i++) {
            $patterns[] = mb_substr($plate, 0, $i) . '_' . mb_substr($plate, $i + 1);
        }

        return $query
            ->whereRaw('CHAR_LENGTH(matricula) = ?', [$len]) // mateixa llargària
            ->where('matricula', '!=', $plate)               // exclou exacta
            ->where(function ($q) use ($patterns) {
                foreach ($patterns as $p) {
                    $q->orWhere('matricula', 'like', $p);
                }
            });
    }
}
