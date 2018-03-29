<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Hora extends Model
{

    public $primaryKey = 'codigo';
    public $timestamps = false;
    //protected $fillable = ['codigo', 'turno', 'hora_ini','hora_fin'];
   

    public function Horario()
    {
        return $this->hasMany(Horario::class, 'codigo', 'sesion_orden');
    }

    public static function horasAfectadas($hora_ini, $hora_fin)
    {
        $horas = Hora::all();
        $horas_afectadas = [];
        foreach ($horas as $hora) {
            if (($hora->hora_ini <= $hora_fin) && ($hora->hora_fin >= $hora_ini))
                $horas_afectadas[] = $hora->codigo;
        }
        return $horas_afectadas;
    }

}
