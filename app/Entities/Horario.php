<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Hora;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;


class Horario extends Model
{

    use BatoiModels;
    
    protected $primaryKey = 'id';
    protected $fillable = ['idProfesor', 'modulo', 'idGrupo', 'ocupacion','aula','dia_semana','sesion_orden','plantilla'];
    protected $rules = [
        'idProfesor' => 'required',
    ];
    protected $inputTypes = [
        'idProfesor' => ['disabled' => 'disabled'],
        'modulo' => ['type' => 'select'],
        'idGrupo' => ['type' => 'select'],
        'ocupacion' => ['type' => 'select'],
    ];
    

    public function Modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo', 'codigo');
    }

    public function Ocupacion()
    {
        return $this->belongsTo(Ocupacion::class, 'ocupacion', 'codigo');
    }

    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }

    public function Hora()
    {
        return $this->belongsTo(Hora::class, 'sesion_orden', 'codigo');
    }

    public function Mestre()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function scopeProfesor($query, $profesor)
    {
        if (Horario::where('idProfesor', $profesor)->count()) {
            return $query->where('idProfesor', $profesor);
        } else {
            return $query->where('idProfesor', Profesor::findOrFail($profesor)->sustituye_a);
        }
    }

    public function scopeGrup($query, $grupo)
    {
        return $query->where('idGrupo', $grupo);
    }

    public function scopeDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    public function scopeOrden($query, $sesion)
    {
        return $query->where('sesion_orden', $sesion);
    }

    public function scopeGuardia($query)
    {
        return $query->where('ocupacion', config('constants.ocupacionesGuardia.normal'));
    }

    public function scopeGuardiaBiblio($query)
    {
        return $query->where('ocupacion', config('constants.ocupacionesGuardia.biblio'));
    }

    public function scopeGuardiaAll($query)
    {
        return $query->whereIn('ocupacion', config('constants.ocupacionesGuardia'));
    }

    public function scopeLectivos($query)
    {
        return $query->whereNotIn('modulo', config('constants.modulosNoLectivos'));
    }

    public function scopePrimera($query, $profesor, $date = null)
    {
        $dia = isset($date) ? nameDay($date) : nameDay(hoy());
        return $query->Profesor($profesor)->Dia($dia);
    }

    /**
     * Devuelve un array con el horario del profesor
     * @param profesor
     * @return array[][]
     */



    public static function HorarioSemanal($profesor)
    {
        $horario = static::Profesor($profesor)
            ->with('Modulo')
            ->with('Ocupacion')
            ->with('Grupo')
            ->get();
        $semana = [];
        foreach ($horario as $hora) {
            $semana[$hora->dia_semana][$hora->sesion_orden] = $hora;
        }
        return $semana;
    }

    public static function HorarioGrupo($grupo)
    {
        $horas = Hora::all();
        $diasSemana = array('L', 'M', 'X', 'J', 'V');
        $semana = [];
        foreach ($diasSemana as $dia) {
            foreach ($horas as $hora) {
                $queHace = static::Grup($grupo)
                        ->Dia($dia)
                        ->where('sesion_orden', '=', $hora->codigo)
                        ->where('ocupacion', '=', null)
                        ->where('modulo', '!=', 'TU01CF')
                        ->where('modulo', '!=', 'TU02CF')
                        ->first();
                if ($queHace) {
                    $semana[$dia][$hora->codigo] = $queHace;
                }
            }
        }
        return $semana;
    }
    
    protected function getProfesorAttribute()
    {
        return $this->Mestre->ShortName;
    }
    protected function getXGrupoAttribute()
    {
        return $this->Grupo->nombre??'';
    }
    
    protected function getXModuloAttribute()
    {
        return$this->Modulo->literal??'';
    }
    protected function getXOcupacionAttribute()
    {
        return $this->Ocupacion->nom??$this->Grupo->nombre;
    }
    protected function getDesdeAttribute()
    {
        return $this->Hora->hora_ini??'';
    }
    protected function getHastaAttribute()
    {
        return $this->Hora->hora_fin??'';
    }
    
    public function getModuloOptions()
    {
        return hazArray(Modulo::All(), 'codigo', 'literal');
    }
    public function getIdGrupoOptions()
    {
        return hazArray(Grupo::All(), 'codigo', 'nombre');
    }
    public function getOcupacionOptions()
    {
        return hazArray(Ocupacion::All(), 'codigo', 'nom');
    }
}
