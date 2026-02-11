<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class ArticuloLote extends Model
{

    protected $table = 'articulos_lote';
    public $timestamps = false;
    protected $fillable = ['lote_id', 'articulo_id', 'marca', 'modelo', 'unidades'];

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $rules = [
        'articulo_id' => 'required',
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
    ];


    public function Articulo(){
        return $this->hasOne(Articulo::class,'id','articulo_id');
    }
    public function Lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id', 'registre');
    }

    public function Materiales()
    {
        return $this->hasMany(Material::class, 'articulo_lote_id', 'id');
    }

    public function getDescripcionAttribute(){
        return $this->Articulo->descripcion;
    }


}
