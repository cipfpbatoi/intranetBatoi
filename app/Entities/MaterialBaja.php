<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class MaterialBaja extends Model
{

    protected $table = 'materiales_baja';
    public $timestamps = false;
    protected $fillable = [
        'idMaterial',
        'idProfesor',
        'motivo',
        'estado',
    ];

    use BatoiModels;



    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Material()
    {
        return $this->belongsTo(Material::class, 'idMaterial');
    }


    public function getDescripcionAttribute()
    {
        return $this->Material->descripcion;
    }

    public function getSolicitanteAttribute()
    {
        return $this->Profesor->shortName;
    }

    public function getFechaBajaAttribute()
    {
        if ($this->Material->fechabaja < $this->created_at) {
            return $this->Material->fechabaja->format('d/m/Y');
        } else {
            return $this->created_at->format('d/m/Y');
        }
    }



}
