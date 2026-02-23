<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\MaterialCrudSchema;
 
class Material extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;
 
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

   
    protected $rules = MaterialCrudSchema::RULES;
    protected $inputTypes = MaterialCrudSchema::INPUT_TYPES;
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
