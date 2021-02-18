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
    protected $fillable = ['lote_registre', 'descripcion', 'marca', 'modelo', 'unidades'];

    use BatoiModels;

    protected $rules = [
        'unidades' => 'numeric',
    ];
    protected $inputTypes = [
    ];


    public function Lote()
    {
        return $this->belongsTo(Lote::class, 'lote_registre', 'registre');
    }

    public function Materiales()
    {
        return $this->hasMany(Material::class, 'articulo_id', 'id');
    }


}
