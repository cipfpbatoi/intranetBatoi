<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class ActividadGrupo extends Model
{

    protected $table = 'actividad_grupo';
    protected $fillable = [
        'idActividad',
        'idGrupo'];

    public $timestamps = false;

    public function scopeDepartamento($query, $dep)
    {
        $grupos = Grupo::select('codigo')->Departamento($dep)->get()->toArray();
        return $query->distinct()->whereIn('idGrupo', $grupos);
    }

}
