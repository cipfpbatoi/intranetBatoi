<?php

namespace Intranet\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model del calendari escolar.
 */
class CalendariEscolar extends Model
{
    use HasFactory;

    protected $table = 'calendari_escolar';

    protected $fillable = ['data', 'tipus', 'esdeveniment'];

    /**
     * Indica si una data és no lectiva.
     *
     * @param DateTimeInterface|string $date
     * @return bool
     */
    public static function esNoLectiu($date): bool
    {
        return self::query()
            ->where('data', self::normalitzaData($date))
            ->where('tipus', 'no lectiu')
            ->exists();
    }

    /**
     * Indica si una data és festiva.
     *
     * @param DateTimeInterface|string $date
     * @return bool
     */
    public static function esFestiu($date): bool
    {
        return self::query()
            ->where('data', self::normalitzaData($date))
            ->where('tipus', 'festiu')
            ->exists();
    }

    /**
     * Indica si una data no és lectiva per a avisos de fitxatge.
     *
     * @param DateTimeInterface|string $date
     * @return bool
     */
    public static function esNoLectiuOFestiu($date): bool
    {
        return self::query()
            ->where('data', self::normalitzaData($date))
            ->whereIn('tipus', ['no lectiu', 'festiu'])
            ->exists();
    }

    /**
     * Normalitza una data a format SQL.
     *
     * @param DateTimeInterface|string $date
     * @return string
     */
    private static function normalitzaData($date): string
    {
        return $date instanceof DateTimeInterface ? $date->format('Y-m-d') : (string) $date;
    }
}
