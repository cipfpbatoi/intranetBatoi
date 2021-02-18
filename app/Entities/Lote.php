<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Estadomaterial;
use Intranet\Entities\Espacio;
use Intranet\Events\ActivityReport;

class Lote extends Model
{

    protected $table = 'lotes';
    protected $primaryKey = 'registre';
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['registre', 'procedencia', 'proveedor','fechaAlta' ];

    use BatoiModels;

    protected $rules = [
        'registre' => 'required|alpha_dash',
    ];
    protected $inputTypes = [
        'registre' => ['type' => 'text'],
        'procedencia' => ['type' => 'select'],
        'fechaAlta' => ['type' => 'date']
    ];

    public function Articulos(){
        return $this->hasMany(Articulo::class);
    }
    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }
    public function Materiales(){
        return $this->hasManyThrough(Material::class,Articulo::class, 'lote_registre','articulo_id','registre','id');
    }
    public function colaboraciones()
    {
        return $this->hasManyThrough(Colaboracion::class, Centro::class,'idEmpresa','idCentro','id');
    }


    public function getOrigenAttribute(){
        return $this->procedencia?config('auxiliares.procedenciaMaterial')[$this->procedencia]:config('auxiliares.procedenciaMaterial')[0];
    }

    public function getEstadoAttribute(){
        if ($this->articulos()->count()) {
            if ($this->materiales()->count()) {
              if ($this->materiales()->where('espacio','INVENT')->count())
                  return 2;
              return 3;
            }
            return 1;
        }
        return 0;
    }

}
