<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Hora extends Model
{
    public $primaryKey = 'codigo';
    public $timestamps = false;


    public function Horario()
    {
        return $this->hasMany(Horario::class, 'codigo', 'sesion_orden');
    }

    public static function horasAfectadas(string $horaIni, string $horaFin): Collection
    {
        return self::where('hora_ini', '<=', $horaFin)
            ->where('hora_fin', '>=', $horaIni)
            ->pluck('codigo');
    }

}
