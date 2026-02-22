<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class MaterialBaja extends Model
{

    protected $table = 'materiales_baja';
    protected $fillable = [
        'idMaterial',
        'idProfesor',
        'motivo',
        'estado',
        'nuevoEstado',
        'tipo'
    ];

    use \Intranet\Entities\Concerns\BatoiModels;

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
        return $this->Material?->descripcion ?? '';
    }

    public function getSolicitanteAttribute()
    {
        return $this->Profesor?$this->Profesor->shortName:'No name';
    }

    public function getEspacioAttribute()
    {
        return $this->Material?->espacio ?? '';
    }

    public function getFechaBajaAttribute()
    {
        $fechaBaja = $this->Material?->fechabaja;
        $createdAt = $this->created_at;

        if ($fechaBaja && $createdAt && $fechaBaja < $createdAt) {
            return $fechaBaja;
        }

        return $createdAt ? $createdAt->format('d/m/Y') : 'No date';
    }

    public function getStateAttribute()
    {
        return $this->estado == 1   ? 'Efectuada' : 'Sol·licitut';
    }

    public function getTipusAttribute()
    {
        return $this->tipo == 0   ? 'Baixa' : 'Canvi Ubicació'  ;
    }

    public function getNuevoAttribute()
    {
        return $this->nuevoEstado??'Baixa Definitiva';
    }



}
