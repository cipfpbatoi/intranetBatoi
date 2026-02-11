<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class TipoActividad extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'tipo_actividad';

    protected $fillable = ['id','cliteral','vliteral','departamento_id','justificacio'];




    public function actividades(){
        return $this->hasMany(Actividad::class , 'tipo_actividad_id');
    }

    public function departament(){
        return $this->belongsTo(Departamento::class , 'departamento_id');
    }

    public function getDepartamentoAttribute() {
        return $this->departament->vliteral??'CENTRE';
    }


}
