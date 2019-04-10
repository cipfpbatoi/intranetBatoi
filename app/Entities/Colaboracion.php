<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Entities\Ciclo;
use Intranet\Events\ActivityReport;

class Colaboracion extends Model
{

    use BatoiModels,TraitEstado;

    protected $table = 'colaboraciones';
    protected $fillable = ['idCentro', 'idCiclo', 'contacto', 'telefono','email', 'puestos','tutor'];
    protected $rules = [
        'idCentro' => 'required|composite_unique:colaboraciones,idCentro,idCiclo',
        'idCiclo' => 'required',
        'puestos' => 'required',
        'email' => 'email',
        'puestos' => 'required|numeric',
        'telefono' => 'max:20'
    ];
    protected $inputTypes = [
        'idCentro' => ['disabled' => 'disabled'],
        'idCiclo' => ['disabled' => 'disabled'],
        'telefono' => ['type'=>'number'],
        'email' => ['type'=>'email']
    ];
    public $timestamps = false;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    public function __construct()
    {
        if (AuthUser()) {
            $this->puestos = 1;
        }
    }

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
    

//    public function getIdCicloOptions()
//    {
//        return hazArray(Ciclo::all()->get(), 'id', 'ciclo');
//    }
    public function scopeCiclo($query, $ciclo)
    {
        return $query->where('idCiclo',$ciclo);
    }
    public function scopeEmpresa($query,$empresa)
    {
        $centros = Centro::select('id')->Empresa($empresa)->get()->toarray();
        return $query->whereIn('idCentro',$centros);
    }
    public function scopeMiColaboracion($query, $empresa=null)
    {
        $cicloC = Grupo::select('idCiclo')->QTutor(AuthUser()->dni)->get();
        $ciclo = $cicloC->count()>0?$cicloC->toarray():[];
        if ($empresa) return $query->whereIn('idCiclo',$ciclo)->Empresa($empresa);
        else return $query->whereIn('idCiclo',$ciclo);
    }
    public function getEmpresaAttribute()
    {
        return $this->Centro->nombre;
    }
    public function getXCicloAttribute()
    {
        return $this->Ciclo->ciclo;
    }
    public function getLocalidadAttribute()
    {
        return $this->Centro->localidad;
    }
    public function getXEstadoAttribute()
    {
        return config('auxiliares.estadoColaboracion')[$this->estado];
    }
    public function getConciertoAttribute(){
        return $this->Centro->Empresa->concierto;
    }
    public function getInstructorPrincipal(){
        return $this->Centro->Instructores->first();
    }
}
