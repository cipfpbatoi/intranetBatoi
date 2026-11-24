<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

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

    /**
     * Resol una lectura de càmera a un propietari, sense triar arbitràriament
     * un vehicle quan el mateix professor en té diversos candidats.
     *
     * @return array{resultat: string, professor: string|null, cotxe: self|null, matricula_canonica: string|null, diferencia_ocr: int|null}
     */
    public static function resoldreLecturaAcces(string $matricula): array
    {
        $plate = self::normalizeMatricula($matricula);
        $exactes = self::query()->where('matricula', $plate)->get();

        if ($exactes->isNotEmpty()) {
            return self::resolCandidats($exactes, 'exacta', $plate, null);
        }

        if (!preg_match('/^[0-9]{4}[A-ZÑ]{3}$/u', $plate)) {
            return self::senseCandidat();
        }

        $candidats = self::query()
            ->get()
            ->filter(fn (self $cotxe): bool => self::esHamming1Modern($plate, $cotxe->matricula));

        if ($candidats->isEmpty()) {
            return self::senseCandidat();
        }

        return self::resolCandidats($candidats, 'tolerada', null, 1);
    }

    /**
     * @param Collection<int, self> $candidats
     * @return array{resultat: string, professor: string|null, cotxe: self|null, matricula_canonica: string|null, diferencia_ocr: int|null}
     */
    private static function resolCandidats(Collection $candidats, string $resultat, ?string $matriculaCanonica, ?int $diferenciaOcr): array
    {
        $professors = $candidats->pluck('idProfesor')->unique()->values();

        if ($professors->count() !== 1) {
            return [
                'resultat' => 'ambigu',
                'professor' => null,
                'cotxe' => null,
                'matricula_canonica' => null,
                'diferencia_ocr' => $diferenciaOcr,
            ];
        }

        return [
            'resultat' => $resultat,
            'professor' => $professors->first(),
            'cotxe' => $candidats->count() === 1 ? $candidats->first() : null,
            'matricula_canonica' => $candidats->count() === 1 ? ($matriculaCanonica ?? $candidats->first()->matricula) : null,
            'diferencia_ocr' => $diferenciaOcr,
        ];
    }

    /**
     * @return array{resultat: string, professor: null, cotxe: null, matricula_canonica: null, diferencia_ocr: null}
     */
    private static function senseCandidat(): array
    {
        return [
            'resultat' => 'sense_candidat',
            'professor' => null,
            'cotxe' => null,
            'matricula_canonica' => null,
            'diferencia_ocr' => null,
        ];
    }

    /**
     * Determina si dos formats moderns diferixen en un sol caràcter del mateix tipus.
     */
    private static function esHamming1Modern(string $lectura, string $candidata): bool
    {
        if (!preg_match('/^[0-9]{4}[A-ZÑ]{3}$/u', $candidata)) {
            return false;
        }

        $diferencies = 0;
        for ($posicio = 0; $posicio < 7; $posicio++) {
            if (mb_substr($lectura, $posicio, 1) !== mb_substr($candidata, $posicio, 1)) {
                $diferencies++;
            }
        }

        return $diferencies === 1;
    }
}
