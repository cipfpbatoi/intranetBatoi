<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;


class IncidenciaColaboracion extends Model
{

    use BatoiModels;

    protected $table = 'incidencias_colaboracion';
    protected $fillable = ['idColaboracion', 'fecha', 'tipo', 'dni','observaciones'];
    protected $rules = [
        'idColaboracion' => 'required',
        'fecha' => 'date|required',
        'tipo' => 'required',
    ];
    protected $inputTypes = [
        'idCentro' => ['disabled' => 'disabled'],
        'idCiclo' => ['disabled' => 'disabled'],
        'telefono' => ['type'=>'number'],
        'email' => ['type'=>'email']
    ];
    public $timestamps = false;

    public function __construct()
    {
        if (AuthUser()) $this->dni = AuthUser()->dni;

    }

    public function Colaboracion()
    {
        return $this->belongsTo(Colaboracion::class, 'idColaboracion', 'id');
    }

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'dni', 'dni');
    }



    public function getTipoContactoAttribute()
    {
        return config('auxiliares.incidenciasColaboracion')[$this->tipo];
    }
    public function getFechaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }


}
