<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;

class OrdenTrabajo extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;
     
    protected $table = 'ordenes_trabajo';
    protected $primaryKey = 'id';
    protected $visible = [
        'id',
        'descripcion',
        'estado',
        'tipo',
        'idProfesor'
    ];
    protected $fillable = [
        'descripcion',
        'idProfesor',
        'tipo',
    ];
    protected $rules = [
        'descripcion' => 'required',
        'tipo' => 'required'
        
    ];
    protected $inputTypes = [
        'idProfesor' => ['type' => 'hidden'],
        'tipo' => ['type' => 'select'],
    ];
    
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Tipos()
    {
        return $this->belongsTo(TipoIncidencia::class, 'tipo');
    }
    
    public function getTipoOptions()
    {
        return hazArray(TipoIncidencia::all(), 'id', 'nombre');
    }
    public function getEstadoOptions()
    {
        return config('auxiliares.estadoOrden');
    }
    public function getCreatedAtAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y H:i');
    }
    public function getXestadoAttribute()
    {
        return $this->getEstadoOptions()[$this->estado];
    }
    public function getXtipoAttribute()
    {
        return $this->Tipos->literal;
    }
}
