<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Services\AdviseService;
use Jenssegers\Date\Date;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use function authUser;

class Incidencia extends Model
{

    protected $table = 'incidencias';
    public $timestamps = false;
    protected $fillable = [
        'tipo',
        'espacio',
        'material',
        'descripcion',
        'idProfesor',
        'prioridad',
        'observaciones',
        'fecha'
    ];
    protected $descriptionField = 'descripcion';

    use BatoiModels;

    protected $inputTypes = [
        'fecha' => ['type' => 'date']
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'saving' => PreventAction::class,
        'deleted' => ActivityReport::class,
        'created' => ActivityReport::class,
    ];
    protected $attributes = ['espacio'=>null,'estado'=>0,'prioridad'=>0];


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

    public function getXespacioAttribute()
    {
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

    public static function putEstado($id, $estado)
    {
        $elemento = Incidencia::findOrFail($id);
        $mensaje = "T'han assignat una incidència: ".$elemento->descripcion;
        if ($elemento->estado < $estado) {
            $elemento->responsable = $estado > 1 ? authUser()->dni : $elemento->Tipos->idProfesor;
        } else {
            $elemento->responsable = $estado > 1 ? authUser()->dni : '';
        }

        $elemento->estado = $estado;
        $elemento->save();
        AdviseService::exec($elemento, $mensaje);

        return ($elemento->estado);
    }

    public function getSubTipoAttribute()
    {
        return config('auxiliares.tipoIncidencia')[$this->Tipos->tipus];
    }

}
