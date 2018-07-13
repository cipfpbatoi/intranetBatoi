<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Estadomaterial;
use Intranet\Entities\Espacio;
use Intranet\Events\ActivityReport;

class Material extends Model
{

    protected $table = 'materiales';
    public $timestamps = false;
    protected $fillable = ['nserieprov', 'descripcion', 'marca', 'modelo', 'ISBN', 'espacio', 'procedencia', 'proveedor','estado','unidades'];

    use BatoiModels;

    protected $rules = [
        'descripcion' => 'required',
        'espacio' => 'required',
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
        'espacio' => ['type' => 'select'],
        'procedencia' => ['type' => 'select'],
        'estado' => ['type' => 'select']
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function __construct()
    {
        $this->estado = 1;
    }
    public function Estados()
    {
        return $this->belongsTo(EstadoMaterial::class, 'estado');
    }

    public function Espacios()
    {
        return $this->belongsTo(Espacio::class, 'espacio', 'aula');
    }

    public function getEstadoOptions()
    {
        return config('auxiliares.estadoMaterial');
    }

    public function getEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

}
