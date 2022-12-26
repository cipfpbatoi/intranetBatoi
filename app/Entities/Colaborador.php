<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;



class Colaborador extends Model
{

    use BatoiModels;

    protected $table = 'colaboradores';
    protected $fillable = [
        'idFct',
        'idInstructor',
        'horas',
        'name',
        ];
    public $timestamps=false;


    public function Fct()
    {
        return $this->hasOne(Fct::class, 'idFct', 'id');
    }
}
