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
    public $timestamps = false;
    protected $fillable = [ 'descripcion','marca','modelo', 'unidades','lote_id','lote_registre'];

    use BatoiModels;

    protected $rules = [
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
    ];


    public function Lote(){
        return $this->belongsTo(Lote::class);
    }

    public function Espacios()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id', 'aula');
    }
    public function Materiales()
    {
        return $this->hasMany(Material::class,'articulo_id','id');
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
