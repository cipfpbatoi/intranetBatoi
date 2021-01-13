<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Estadomaterial;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intranet\Entities\Espacio;
use Intranet\Events\ActivityReport;

class Articulo extends Model
{

    protected $table = 'articulos';
    protected $fillable = [ 'descripcion','marca','modelo','identificacion', 'espacio_id', 'estado','unidades','numeracionInventario'];

    use BatoiModels,SoftDeletes;

    protected $rules = [
        'espacio_id' => 'required',
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
        'espacio_id' => ['type' => 'select'],
        'estado' => ['type' => 'select']
    ];


    protected $attributes = ['estado'=>1];

    public function Lote(){
        return $this->belongsTo(Lote::class);
    }

    public function Espacios()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'aula');
    }

    public function getEstadoOptions()
    {
        return config('auxiliares.estadoMaterial');
    }

    public function getEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    public function getEspacioAttribute(){
        return $this->Espacios->descripcion;
    }

    public function getEstatAttribute(){
        return config('auxiliares.estadoMaterial')[$this->estado];
    }


}
