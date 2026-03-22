<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class TipoExpediente extends Model
{
    protected $table = 'tipo_expedientes';
    public $timestamps = false;

    public function expedientes(){
        return $this->hasMany(Expediente::class, 'tipo', 'id');
    }
}
