<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class IpGuardia extends Model
{
    use BatoiModels;
    public $timestamps = false;
    protected $table = 'ipGuardias';

    protected $fillable = [
        'ip',
        'codOcup',
    ];
    protected $rules = [
        'ip' => 'required',
        'codOcup' => 'required',
    ];
}
