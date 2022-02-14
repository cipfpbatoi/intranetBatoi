<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Events\ActivityReport;


class Colaboracion extends Model
{

    use BatoiModels;

    protected $table = 'colaboraciones';
    protected $fillable = ['idCentro', 'idCiclo', 'contacto', 'telefono','email', 'puestos','tutor'];
    protected $rules = [
        'idCentro' => 'required|composite_unique:colaboraciones,idCentro,idCiclo',
        'idCiclo' => 'required',
        'email' => 'email',
        'puestos' => 'required|numeric',
        'telefono' => 'max:20'
    ];
    protected $inputTypes = [
        'idCentro' => ['disabled' => 'disabled'],
        'idCiclo' => ['disabled' => 'disabled'],
        'telefono' => ['type'=>'number'],
        'email' => ['type'=>'email'],
        'tutor' => ['type'=>'hidden'],
    ];
    public $timestamps = false;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    protected $attributes = [
        'puestos' => 1,
     ];


    public function Centro()
    {
        return $this->belongsTo(Centro::class, 'idCentro', 'id');
    }

    public function Ciclo()
    {
        return $this->belongsTo(Ciclo::class, 'idCiclo', 'id');
    }
    public function fcts()
    {
        return $this->hasMany(Fct::class, 'idColaboracion', 'id');
    }
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'idColaboracion', 'id');
    }
    public function Propietario()
    {
        return $this->belongsTo(Profesor::class, 'tutor', 'dni');
    }
    public function votes()
    {
        return $this->hasMany(VoteAnt::class,'idColaboracion','id');
    }

    public function scopeCiclo($query, $ciclo)
    {
        return $query->where('idCiclo',$ciclo);
    }
    public function scopeEmpresa($query,$empresa)
    {
        $centros = Centro::select('id')->Empresa($empresa)->get()->toarray();
        return $query->whereIn('idCentro',$centros);
    }
    public function scopeMiColaboracion($query, $empresa=null,$dni=null)
    {
        $dni = $dni??AuthUser()->dni;
        $cicloC = Grupo::select('idCiclo')->QTutor($dni)->get();
        $ciclo = $cicloC->count()>0?$cicloC->toarray():[];
        if ($empresa) {
            return $query->whereIn('idCiclo', $ciclo)->Empresa($empresa);
        }
        else {
            return $query->whereIn('idCiclo', $ciclo);
        }
    }


    public function getEmpresaAttribute()
    {
        return $this->Centro->nombre;
    }
    public function getXCicloAttribute()
    {
        return $this->Ciclo->ciclo;
    }

    public function getXEstadoAttribute()
    {
        return config('auxiliares.estadoColaboracion')[$this->estado];
    }
    public function getLocalidadAttribute()
    {
        return $this->Centro->localidad?strtoupper($this->Centro->localidad):'Desconeguda';
    }


}
