<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;


class Erasmus extends Model
{

    use BatoiModels;

    protected $table = 'erasmus';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'email', 'name','direccion','telefono','poblacion'];

    
    public $timestamps = false;


}
