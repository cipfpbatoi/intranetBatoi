<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class AlumnoFct extends Model
{

    use BatoiModels;
    
    protected $table = 'alumno_fcts';
    protected $fillable = ['id','idFct','idAlumno', 'calificacion','calProyecto'];
    
    public $timestamps = false;

    protected $rules = [
        'id' => 'required',
        'idAlumno' => 'required',
        'idFct' => 'required',
        'calificacion' => 'numeric',
        'calProyecto' => 'numeric',
    ];
    protected $inputTypes = [
        'id' => ['type' => 'hidden'],
        'idAlumno' => ['type' => 'hidden'],
        'idFct' => ['type' => 'hidden'],
        'calificacion' => ['type' => 'hidden'],
    ];
    
    
    
    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Fct()
    {
        return $this->belongsTo(Fct::class, 'idFct', 'id');
    }

    
    public function scopeMisFcts($query,$profesor=null,$activa=null)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        $cicloC = Grupo::select('idCiclo')->QTutor($profesor)->first()->idCiclo;
        $colaboraciones = Colaboracion::select('id')->where('idCiclo',$cicloC)->get()->toArray();
        $fcts = $activa?Fct::select('id')->Activa($activa)->whereIn('idColaboracion',$colaboraciones)
                ->get()->toArray():Fct::select('id')->whereIn('idColaboracion',$colaboraciones)
                ->orWhere('asociacion',2)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos)->whereIn('idFct',$fcts);
    }
    public function scopeMisConvalidados($query,$profesor=null)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        $fcts = Fct::select('id')->Where('asociacion',2)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos)->whereIn('idFct',$fcts);
    }
    public function scopeEsFct($query){
        $fcts = Fct::select('id')->esFct()->get()->toArray();
        return $query->whereIn('idFct',$fcts);
    }
    
    public function getNombreAttribute(){
        return $this->Alumno->NameFull;
    }
    public function getPeriodeAttribute(){
        return $this->Fct->periode;
    }
    public function getQualificacioAttribute(){
        return isset($this->calificacion)?$this->calificacion?$this->calificacion==2?'Convalidat/Exempt': 'Apte' : 'No Apte' : 'No Avaluat';
    }
    public function getProjecteAttribute(){
        return isset($this->calProyecto) ? $this->calProyecto == 0 ? 'No presenta' : $this->calProyecto : 'No Avaluat';
    }
    public function getAsociacionAttribute(){
        return $this->Fct->asociacion;
    }
}
