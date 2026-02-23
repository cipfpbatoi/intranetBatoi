<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\TutoriaCrudSchema;

class Tutoria extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

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
    protected $rules = TutoriaCrudSchema::RULES;
    protected $inputTypes = TutoriaCrudSchema::INPUT_TYPES;
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
        if (empty($entrada)) {
            return '';
        }
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        if (empty($entrada)) {
            return '';
        }
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
        return config("auxiliares.grupoTutoria.{$this->grupos}") ?? '';
    }
    protected function getTiposAttribute()
    {
        return config("auxiliares.tipoTutoria.{$this->tipo}") ?? '';
    }

    protected function getEstatAttribute()
    {
        return $this->obligatoria ? 'Obligatoria' : 'Voluntaria';
    }

    protected function getFeedBackAttribute()
    {
        $user = authUser();
        if ($user && $user->GrupoTutoria) {
            return $this->Grupos->where('codigo', $user->GrupoTutoria)->count() ? "Realitzada" : "Incompleta";
        }

        return $this->Grupos->count();
    }
    
}
