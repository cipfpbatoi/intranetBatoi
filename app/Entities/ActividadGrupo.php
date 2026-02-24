<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;

class ActividadGrupo extends Model
{

    protected $table = 'actividad_grupo';
    protected $fillable = [
        'idActividad',
        'idGrupo'];

    public $timestamps = false;

    public function scopeDepartamento($query, $dep)
    {
        $grupos = app(GrupoService::class)->byDepartamento((int) $dep)->pluck('codigo')->all();
        return $query->distinct()->whereIn('idGrupo', $grupos);
    }

}
