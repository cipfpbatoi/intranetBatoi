<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class Tutoria extends Model
{
    use BatoiModels;

    protected $table = 'tutorias';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'obligatoria',
        'tipo',
        'grupos',
        'desde',
        'hasta',
        'fichero',
        
    ];
    protected $rules = [
        'desde' => 'required|date',
        'hasta' => 'required|date',
        'tipo' => 'required',
        'descripcion' => 'required',
        'grupos' => 'required',
        ];
    protected $inputTypes = [
        'obligatoria' => ['type' => 'checkbox'],
        'fichero' => ['type' => 'file'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'grupos' => ['type' => 'select'],
        'tipo' => ['type' => 'select']
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];


    public function Grupos()
    {
        return $this->hasManyThrough(Grupo::class, TutoriaGrupo::class, 'idTutoria', 'codigo', 'id', 'idGrupo');
    }

    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getGruposOptions()
    {
        return config('auxiliares.grupoTutoria');
    }
    public function getTipoOptions()
    {
        return config('auxiliares.tipoTutoria');
    }
    protected function getXobligatoriaAttribute()
    {
        return $this->obligatoria ? 'X' : '-';
    }
    protected function getGrupoAttribute()
    {
        return config('auxiliares.grupoTutoria')[$this->grupos];
    }
    protected function getTiposAttribute()
    {
        return config('auxiliares.tipoTutoria')[$this->tipo];
    }

    protected function getEstatAttribute()
    {
        return $this->obligatoria ? 'Obligatoria' : 'Voluntaria';
    }

    protected function getFeedBackAttribute()
    {
        if (authUser()->GrupoTutoria) {
            return $this->Grupos->where('codigo', authUser()->GrupoTutoria)->count() ? "Realitzada" : "Incompleta";
        }

        return $this->Grupos->count();
    }
    
}
