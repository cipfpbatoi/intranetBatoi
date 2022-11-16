<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Events\GrupoCreated;


class GrupoTrabajo extends Model
{

    protected $table = 'grupos_trabajo';
    public $timestamps = false;

    use BatoiModels;

    protected $fillable = [
        'literal',
        'objetivos',
    ];
    protected $rules = [
        'literal' => 'required|max:40',
    ];
    protected $inputTypes = [
        'objetivos' => ['type' => 'textarea'],
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
        'created' => GrupoCreated::class,
    ];

    public function profesores()
    {
        return $this->hasManyThrough(Profesor::class, Miembro::class, 'idGrupoTrabajo', 'dni');
    }

    public function Creador()
    {
        return Miembro::where('idGrupoTrabajo', '=', $this->id)
                ->where('coordinador', '=', 1)
                ->get()
                ->first()
                ->idProfesor;
    }

    public function scopeMisGruposTrabajo($query)
    {
        $grupos = Miembro::select('idGrupoTrabajo')->where('idProfesor', '=', authUser()->dni)->get()->toarray();
        return $query->whereIn('id', $grupos);
    }

}
