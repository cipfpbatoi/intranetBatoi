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
    protected $fillable = [ 'descripcion',   'procedencia', 'proveedor','registre','inventariable' ];

    use BatoiModels;

    protected $rules = [
        'descripcion' => 'required',
    ];
    protected $inputTypes = [
        'procedencia' => ['type' => 'select'],
        'estado' => ['type' => 'select'],
        'inventariable' => ['type' => 'checkbox']
    ];

    public function Articulos(){
        return $this->hasMany(Articulo::class);
    }
    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

    public function getInventarioAttribute(){
        return $this->inventariable?'Sí':'No';
    }
    public function getOrigenAttribute(){
        return $this->procedencia?config('auxiliares.procedenciaMaterial')[$this->procedencia]:config('auxiliares.procedenciaMaterial')[0];
    }

}
