<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{

    public $primaryKey = 'id';
    public $timestamps = false;

    public function Reserva()
    {
        return $this->hasMany(Reserva::getClass(), 'recurso_id', 'id');
    }

}
