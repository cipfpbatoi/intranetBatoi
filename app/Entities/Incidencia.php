<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Events\IncidenciaSaved;

class Incidencia extends Model
{

    protected $table = 'incidencias';
    public $timestamps = false;
    protected $fillable = ['tipo','espacio', 'material', 'descripcion', 'idProfesor',  'prioridad', 'observaciones','fecha'];
    protected $descriptionField = 'descripcion';

    use BatoiModels,
        TraitEstado;

    protected $inputTypes = [
        'fecha' => ['type' => 'date']
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'saving' => PreventAction::class,
        'deleted' => ActivityReport::class,
        'created' => ActivityReport::class,
    ];
    protected $attributes = ['espacio'=>null,'estado'=>0,'prioridad'=>0,'tipo'=>10];


    public function Creador()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Responsables()
    {
        return $this->belongsTo(Profesor::class, 'responsable', 'dni');
    }

    public function Tipos()
    {
        return $this->belongsTo(TipoIncidencia::class, 'tipo');
    }

    public function Materiales()
    {
        return $this->belongsTo(Material::class, 'material');
    }

    public function Espacios()
    {
        return $this->belongsTo(Espacio::class, 'espacio');
    }

    public function getEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    public function getTipoOptions()
    {
        return hazArray(TipoIncidencia::all(), 'id', 'literal');
    }


    public function getEstadoOptions()
    {
        return config('auxiliares.estadoIncidencia');
    }

    public function getPrioridadOptions()
    {
        return config('auxiliares.prioridadIncidencia');
    }



    public function getFechasolucionAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y');
    }

    public function getXestadoAttribute()
    {
        return $this->getEstadoOptions()[$this->estado];
    }

    public function getXcreadorAttribute()
    {
        return $this->Creador->ShortName;
    }

    public function getXespacioAttribute(){
        return $this->Espacios->descripcion ?? '';
    }

    public function getXresponsableAttribute()
    {
        return  $this->Responsables->ShortName ?? '';
    }

    public function getXtipoAttribute()
    {
        return $this->Tipos->literal;
    }

    public function getDesCurtaAttribute()
    {
        return substr($this->descripcion, 0, 30);
    }

    public static function putEstado($id, $estado, $mensaje = null, $fecha = null)
    {
        $elemento = static::findOrFail($id);
        if (($fecha != null) && (isset($elemento->fechasolucion))) {
            $elemento->fechasolucion = $fecha;
        }
        if ($elemento->estado < $estado) {
            $elemento->responsable = $estado > 1 ? AuthUser()->dni : $elemento->Tipos->idProfesor;
        } else {
            $elemento->responsable = $estado > 1 ? AuthUser()->dni : '';
        }

        if ($elemento->has('solucion') && isset($mensaje)) {
            $elemento->solucion .= $mensaje;
        }
        $elemento->estado = $estado;
        $elemento->save();
        $elemento->informa($mensaje);

        return ($elemento->estado);
    }

}
