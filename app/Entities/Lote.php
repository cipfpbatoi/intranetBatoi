<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Estadomaterial;
use Intranet\Entities\Espacio;
use Intranet\Events\ActivityReport;

class Lote extends Model
{

    protected $table = 'lotes';
    public $timestamps = false;
    protected $fillable = ['identificacion', 'descripcion', 'marca', 'modelo',  'procedencia', 'proveedor','unidades'];

    use BatoiModels;

    protected $rules = [
        'descripcion' => 'required',
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
        'procedencia' => ['type' => 'select'],
        'estado' => ['type' => 'select']
    ];


    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

}
