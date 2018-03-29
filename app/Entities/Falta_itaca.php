<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

class Falta_itaca extends Model
{
    use TraitEstado;
    
    protected $table = 'faltas_itaca';
    public $timestamps = false;
    
    
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
    public function Hora()
    {
        return $this->belongsTo(Hora::class, 'sesion_orden', 'codigo');
    }
    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }
    
    public function getNombreAttribute(){
        return $this->Profesor->fullName;
    }
    public function getHorasAttribute(){
        return $this->Hora->hora_ini.' - '.$this->Hora->hora_fin;
    }
    public function getXGrupoAttribute(){
        return $this->Grupo->nombre;
    }
    public function getFichajeAttribute(){
        return $this->enCentro?'SI':'NO';
    }
    public function getXestadoAttribute(){
        return trans('models.Falta_itaca.'.$this->estado);
    }
    public function getDiaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public static function putEstado($id, $estado, $mensaje = null)
    {
        
        $elemento = static::findOrFail($id);
        $dia = new Date($elemento->dia);
        $elementos = static::where('idProfesor',$elemento->idProfesor)
                ->where('dia',$dia->format('Y-m-d'))
                ->get();
        foreach ($elementos as $element){
            $element->estado = $estado;
            $element->save();
        }
        $element->informa($mensaje);
        return ($element->estado);
    }

}
