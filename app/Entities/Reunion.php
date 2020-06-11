<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Events\ReunionCreated;


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



    public function Creador()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'asistencias', 'idReunion', 'idProfesor')->withPivot('asiste');
    }
    public function ordenes()
    {
        return $this->hasMany(OrdenReunion::class, 'idReunion', 'id');
    }
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_reuniones', 'idReunion', 'idAlumno')->withPivot('capacitats');
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
    public function scopeConvocante($query,$dni=null)
    {
        $dni = $dni??AuthUser()->dni;
        $sustituye = (isset(Profesor::find($dni)->sustituye_a))?Profesor::find($dni)->sustituye_a:null;
        return $query->where('idProfesor',$dni)->orWhere('idProfesor',$sustituye);
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
        else return config('auxiliares.numeracion');
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
    public function getModificableAttribute(){
        return TipoReunion::modificable($this->tipo);
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
    public function getCicloAttribute()
    {
        return Grupo::QTutor($this->idProfesor)->count()?Grupo::QTutor($this->idProfesor)->first()->Ciclo->ciclo:'';   
    }
    public function getXtipoAttribute(){
        return TipoReunion::literal($this->tipo);
    }
    public function getXnumeroAttribute(){
        return config("auxiliares.numeracion.$this->numero");
    }
    public function getAvaluacioFinalAttribute(){
        return ($this->tipo == 7 && $this->numero == 34 );
    }
    public function getExtraOrdinariaAttribute(){
        return ($this->tipo == 7 && $this->numero == 35);
    }
    public function getGrupoClaseAttribute(){
        if (TipoReunion::colectivo($this->tipo) != 'Grupo') return null;
        return Grupo::QTutor($this->idProfesor)->first();
    }

}
