<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Centro_instructor extends Model
{

    protected $table = 'centros_instructores';
    protected $fillable = [
        'idCentro',
        'idProfesor'];
    public $timestamps = false;

}
