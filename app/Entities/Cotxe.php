<?php

namespace Intranet\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Cotxe extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    /**
     * Camps assignables en persistència massiva/controlada.
     *
     * @var array<int, string>
     */
    protected $fillable = ['matricula', 'marca', 'idProfesor'];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function scopePlateHamming1($query, string $matricula)
    {
        $plate = Str::upper(preg_replace('/[\s-]/', '', $matricula));
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
