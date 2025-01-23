<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Events\ActivityReport;
use Intranet\Providers\AuthServiceProvider;


class Colaboracion extends Model
{

    use BatoiModels;

    protected $table = 'colaboraciones';
    protected $fillable = [
        'idCentro',
        'idCiclo',
        'contacto',
        'telefono',
        'email',
        'puestos',
        'tutor'];
    protected $rules = [
        'idCentro' => 'required|composite_unique:colaboraciones,idCentro,idCiclo',
        'idCiclo' => 'required',
        'email' => 'email',
        'puestos' => 'required|numeric',
        'telefono' => 'max:20'
    ];
    protected $inputTypes = [
        'idCentro' => ['type' => 'hidden'],
        'idCiclo' => ['type' => 'hidden'],
        'telefono' => ['type'=>'number'],
        'email' => ['type'=>'email'],
        'tutor' => ['type'=>'hidden'],
    ];

    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    protected $attributes = [
        'puestos' => 1,
        'estado' => 1
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
        return $this->hasMany(VoteAnt::class, 'idColaboracion', 'id');
    }

    public function scopeCiclo($query, $ciclo)
    {
        return $query->where('idCiclo', $ciclo);
    }
    public function scopeEmpresa($query, $empresa)
    {
        $centros = Centro::select('id')->Empresa($empresa)->get()->toarray();
        return $query->whereIn('idCentro', $centros);
    }
    public function scopeMiColaboracion($query, $empresa=null, $dni=null)
    {
        $dni = $dni??authUser()->dni;
        $cicloC = Grupo::select('idCiclo')->QTutor($dni)->get();
        $ciclo = $cicloC->count()>0?$cicloC->toarray():[];
        if ($empresa) {
            return $query->whereIn('idCiclo', $ciclo)->Empresa($empresa);
        }
        return $query->whereIn('idCiclo', $ciclo);
    }

    public function getEmpresaAttribute()
    {
        return $this->Centro->nombre;
    }
    public function getShortAttribute()
    {
        return substr($this->Centro->nombre, 0, 50);
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
    public function getHorariAttribute()
    {
        return $this->Centro->horarios;
    }

    public function getEstadoOptions()
    {
        return config('auxiliares.estadoColaboracion');
    }

    public function getAnotacioAttribute()
    {
        $contactos = '';
        foreach  (Activity::modelo('Colaboracion')
            ->id($this->id)
            ->where('action','book')
            ->orderBy('created_at')
            ->get() as $contacto) {
            $contactos  .= $contacto->comentari  ;
        }
        return $contactos;
    }
    public function getProfesorAttribute()
    {
        return $this->Propietario->fullName??'';
    }
    public function getUltimoAttribute()
    {
        return $this->updated_at;
    }

    private function dniTutor()
    {
        return isset(authUser()->nia)?
            authUser()->tutor[0]->dni:
            authUser()->dni;
    }

    public function getSituationAttribute()
    {
        if ($this->tutor == '' && $this->estado == 1) {
            return 1;
        }
        if ($this->estado == 1 || $this->estado == 3) {
            return 2;
        }
        if ($this->estado == 2) {
            return 3;
        }
        return 1;
    }


}
