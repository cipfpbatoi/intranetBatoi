<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendariEscolar extends Model
{
    use HasFactory;

    protected $table = 'calendari_escolar';

    protected $fillable = ['data', 'tipus', 'esdeveniment'];

    public static function esLectiu($date)
    {
        return self::where('data', $date->format('Y-m-d'))->where('tipus', 'lectiu')->exists();
    }

    public static function esFestiu($date)
    {
        return self::where('data', $date->format('Y-m-d'))->where('tipus', 'festiu')->exists();
    }
}
