<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Hora extends Model
{
    public $primaryKey = 'codigo';
    public $timestamps = false;


    public function Horario()
    {
        return $this->hasMany(Horario::class, 'codigo', 'sesion_orden');
    }

    public static function horasAfectadas($horaIni, $horaFin)
    {
        $horas = Hora::all();
        $horasAfectadas = [];
        foreach ($horas as $hora) {
            if (($hora->hora_ini <= $horaFin) && ($hora->hora_fin >= $horaIni)) {
                $horasAfectadas[] = $hora->codigo;
            }
        }
        return $horasAfectadas;
    }

}
