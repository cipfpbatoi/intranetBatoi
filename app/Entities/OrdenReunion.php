<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class OrdenReunion extends Model
{

    protected $table = 'ordenes_reuniones';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'descripcion',
        'resumen',
        'idReunion',
        'orden'
    ];
    protected $rules = [
        'orden' => 'required|integer',
        'descripcion' => 'required'
    ];

}
