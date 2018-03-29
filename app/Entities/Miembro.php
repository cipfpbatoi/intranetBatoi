<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{

    public $timestamps = false;
    protected $keyType = 'string';
    protected $primaryKey = 'idProfesor';
    protected $fillable = [
        'idGrupoTrabajo',
        'idProfesor',
    ];

}
