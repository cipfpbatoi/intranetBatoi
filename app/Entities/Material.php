<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class Material extends Model
{

    protected $table = 'materiales';
    public $timestamps = false;
    protected $fillable = [
        'nserieprov',
        'descripcion',
        'marca',
        'modelo',
        'ISBN',
        'espacio',
        'procedencia',
        'proveedor',
        'estado',
        'unidades',
        'inventariable',
        'articulo_lote_id'
    ];

    use BatoiModels;

    protected $rules = [
        'descripcion' => 'required',
        'espacio' => 'required',
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
        'espacio' => ['type' => 'select'],
        'procedencia' => ['type' => 'select'],
        'inventariable' => ['type' => 'checkbox'],
        'estado' => ['type' => 'select'],
        'articulo_id' => ['type' => 'hidden'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    protected $attributes = ['estado'=>1];


    public function Espacios()
    {
        return $this->belongsTo(Espacio::class, 'espacio', 'aula');
    }

    public function LoteArticulo()
    {
        return $this->belongsTo(ArticuloLote::class, 'articulo_lote_id');
    }


    public function getEstadoOptions()
    {
        return config('auxiliares.estadoMaterial');
    }

    public function getStateAttribute()
    {
        return config('auxiliares.estadoMaterial')[$this->estado];
    }

    public function getEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    public function getEspaiAttribute()
    {
        $materialBaja = MaterialBaja::where('idMaterial',$this->id)->first();
        if ($materialBaja && $materialBaja->tipo) {
            return 'Proposta Nova Ubicació';
        } else {
            return $this->espacio;
        }
    }

    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

}
