<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Events\ReunionCreated;
use Intranet\Entities\Profesor;
use Intranet\Entities\TipoReunion;
use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Espacio;

class Reunion extends Model
{

    use BatoiModels;

    protected $table = 'reuniones';
    protected $fillable = [
        'tipo',
        'grupo',
        'curso',
        'numero',
        'fecha',
        'descripcion',
        'objetivos',
        'idProfesor',
        'idEspacio',
        'fichero'
    ];
    protected $rules = [
        'tipo' => 'required',
        'curso' => 'required',
        'fecha' => 'required|date',
        'descripcion' => 'required|between:0,120',
        'idProfesor' => 'required',
        'idEspacio' => 'required'
    ];
    protected $inputTypes = [
        'idProfesor' => ['type' => 'hidden'],
        //    'select' => ['type' => 'hidden'],
        'numero' => ['type' => 'select'],
        'tipo' => ['type' => 'select'],
        'grupo' => ['type' => 'select'],
        'curso' => ['disabled' => 'disabled'],
        'fecha' => ['type' => 'datetime'],
        'objectivos' => ['type' => 'textarea'],
        'idEspacio' => ['type' => 'select'],
        'fichero' => ['type' => 'file'],
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
        'created' => ReunionCreated::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function __construct()
    {
        if (AuthUser()) {
            $this->idProfesor = AuthUser()->dni;
            $this->curso = Curso();
        }
    }

    public function Creador()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'asistencias', 'idReunion', 'idProfesor')->withPivot('asiste');
    }

    public function scopeMisReuniones($query)
    {
        $reuniones = Asistencia::select('idReunion')
                ->where('idProfesor', '=', AuthUser()->dni)
                ->orWhere('idProfesor','=',AuthUser()->sustituye_a)
                ->get()
                ->toarray();
        return $query->whereIn('id', $reuniones)->orWhere('idProfesor',AuthUser()->dni);
    }
    public function scopeConvocante($query,$dni)
    {
        return $query->where('idProfesor',$dni);
    }
    public function scopeTipo($query,$tipo)
    {
        return $query->where('tipo',$tipo);
    }
    public function scopeNumero($query,$numero)
    {
        if ($numero > 0) return $query->where('numero',$numero);
        else return $query;
    }
    public function scopeArchivada($query)
    {
        return $query->where('archivada',1);
    }

    public function getTipoOptions()
    {
        return TipoReunion::allSelect();
    }

    public function getIdEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    public function getNumeroOptions()
    {
        if (isset($this->tipo)) return TipoReunion::numeracion($this->tipo);
        else return config('constants.numeracion');
    }

    public function getGrupoOptions()
    {
        return hazArray(GrupoTrabajo::MisGruposTrabajo()->get(), 'id', 'literal');
    }

//    public function Departamento()
//    {
//        return Departamento::find(Profesor::find($this->idProfesor)->departamento)->literal;
//    }
    
    public function getDepartamentoAttribute(){
        return $this->Creador->Departamento->literal;
    }
    public function getAvaluacioAttribute(){
        return $this->numero-20;
    }

    public function getFechaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y H:i');
    }

    public function getUpdatedAtAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function Tipos()
    {
        return TipoReunion::get($this->tipo);
    }

    public function Grupos()
    {
        return $this->belongsTo(GrupoTrabajo::class, 'grupo', 'id');
    }

    public function Espacio()
    {
        return $this->belongsTo(Espacio::class, 'idEspacio', 'aula');
    }

    public function Responsable()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function getXgrupoAttribute()
    {
        if ($this->grupo) return ($this->Grupos->literal);
        $colectivo = TipoReunion::colectivo($this->tipo);
        if ($colectivo == 'Profesor') return 'Claustro';
        if ($colectivo == 'Jefe') return 'COCOPE';
        $profesor = Profesor::where('dni', '=', $this->idProfesor)->get()->first();
        if ($colectivo == 'Departamento') return (Departamento::where('id', '=', $profesor->departamento)->get()->first()->cliteral);
        if ($colectivo == 'Grupo') return Grupo::QTutor($profesor->dni)->count()?Grupo::QTutor($profesor->dni)->first()->nombre:'';
    }
    public function getXtipoAttribute(){
        return TipoReunion::literal($this->tipo);
    }
    public function getXnumeroAttribute(){
        return config("constants.numeracion.$this->numero");
    }

}
