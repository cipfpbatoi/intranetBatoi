<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{

    protected $table = 'provincias';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
    ];

}
