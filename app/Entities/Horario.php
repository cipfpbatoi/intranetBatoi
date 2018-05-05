<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Hora;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;


class Horario extends Model
{

//    use BatoiModels;
    
    protected $primaryKey = 'id';
//    protected $fillable = ['idProfesor', 'modulo', 'idGrupo', 'ocupacion', 'dia_semana', 'sesion_orden'];
//    protected $rules = [
//        'idProfesor' => 'required',
//        'dia_semana' => 'required',
//        'sesion_orden' => 'required',
//    ];
//    protected $inputTypes = [
//        'idProfesor' => ['type' => 'select'],
//        'modulo' => ['type' => 'select'],
//        'idGrupo' => ['type' => 'select'],
//        'ocupacion' => ['type' => 'select'],
//        'sesion_orden' => ['type' => 'select'],
//    ];
//    protected $dispatchesEvents = [
//        'saved' => ActivityReport::class,
//        'deleted' => ActivityReport::class,
//    ];

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

//    
//    
//  scopes
//    
    public function scopeProfesor($query, $profesor)
    {
        if (Horario::where('idProfesor',$profesor)->count())
            return $query->where('idProfesor', $profesor);
        else
            return $query->where('idProfesor', Profesor::findOrFail($profesor)->sustituye_a);
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
        return $query->where('ocupacion', '3249454');
    }

    public function scopeHora($query, $hora)
    {
        return $query->where('desde', '<=', $hora)->where('hasta', '>=', $hora);
    }

    public function scopePrimera($query, $profesor, $date = null)
    {
        $dia = isset($date) ? nameDay($date) : nameDay(Hoy());
        return $query->Profesor($profesor)->Dia($dia);
    }

    /**
     * Devuelve un array con el horario del profesor
     * 
     * @param profesor
     * @return array[][]
     */
    public static function HorarioSemanal($profesor)
    {
        $horas = Hora::all();
        $dias_semana = array('L', 'M', 'X', 'J', 'V');
        $semana = [];
        foreach ($dias_semana as $dia) {
            foreach ($horas as $hora) {
                $queHace = static::Profesor($profesor)
                        ->Dia($dia)
                        ->where('sesion_orden', '=', $hora->codigo)
                        ->first();
                if ($queHace)
                    $semana[$dia][$hora->codigo] = $queHace;
            }
        }
        return $semana;
    }

    public static function HorarioGrupo($grupo)
    {
        $horas = Hora::all();
        $dias_semana = array('L', 'M', 'X', 'J', 'V');
        $semana = [];
        foreach ($dias_semana as $dia) {
            foreach ($horas as $hora) {
                $queHace = static::Grup($grupo)
                        ->Dia($dia)
                        ->where('sesion_orden', '=', $hora->codigo)
                        ->where('ocupacion', '=', null)
                        ->where('modulo', '!=', 'TU01CF')
                        ->where('modulo', '!=', 'TU02CF')
                        ->first();
                if ($queHace)
                    $semana[$dia][$hora->codigo] = $queHace;
            }
        }
        return $semana;
    }

    public static function ModulosActivos($dep = null)
    {
        //dd(Modulo::select('codigo')->departamento($dep)->get()->toArray());
        return isset($dep) ?
                Horario::select('idGrupo', 'modulo', 'idProfesor')
                        ->distinct()
                        ->orderBy('idGrupo')
                        ->whereNotNull('idGrupo')
                        ->whereNotNull('modulo')
                        ->whereNotNull('idProfesor')
                        ->whereNotIn('modulo', config('constants.modulosNoLectivos'))
                        ->whereIn('modulo', Modulo::select('codigo')->departamento($dep)->get()->toArray())
                        ->get() : Horario::select('idGrupo', 'modulo', 'idProfesor')
                        ->distinct()
                        ->orderBy('idGrupo')
                        ->whereNotNull('idGrupo')
                        ->whereNotNull('modulo')
                        ->whereNotNull('idProfesor')
                        ->whereNotIn('modulo', config('constants.modulosNoLectivos'))
                        ->get();
    }
    
    protected function getProfesorAttribute(){
        return $this->Mestre->ShortName;
    }
    protected function getXModuloAttribute(){
        return isset($this->Modulo->literal)?$this->Modulo->literal:'';
    }
    protected function getXOcupacionAttribute(){
        return isset($this->Ocupacion->nom)?$this->Ocupacion->nom:$this->Grupo->nombre;
    }
    
    public function getIdProfesorOptions(){
        return hazArray(Profesor::All(), 'dni', 'FullName');
    }

    public function getModuloOptions(){
        return hazArray(Modulo::All(), 'codigo', 'literal');
    }
    public function getIdGrupoOptions(){
        return hazArray(Grupo::All(), 'codigo', 'nombre');
    }
    public function getOcupacionOptions(){
        return hazArray(Ocupacion::All(), 'codigo', 'nom');
    }
    public function getSesionOrdenOptions(){
        return hazArray(Hora::All(), 'codigo', ['hora_ini','hora_fin']);
    }
    

}
