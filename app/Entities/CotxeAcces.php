<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class CotxeAcces extends Model
{
    public $table = 'cotxe_accessos';
    protected $fillable = [
        'matricula', 'data', 'autoritzat', 'porta_oberta', 'device'
    ];
}
