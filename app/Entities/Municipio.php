<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{

    public $primaryKey = 'id';
    protected $table = 'municipios';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
    ];

    public function Provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
}
