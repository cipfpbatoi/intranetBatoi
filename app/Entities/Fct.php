<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Alumno;

class Fct extends Model
{
    use BatoiModels;
    
    protected $table = 'fcts';
    public $timestamps = false;

    protected $fillable = ['desde','hasta'
        ,'idAlumno', 'idColaboracion','idInstructor' ,
        'horas','asociacion',
        'correoAlumno','correoInstructor'];
//    protected $fillable = ['idAlumno', 'idColaboracion',  'desde','hasta'
//        ,'horas','asociacion','horas_semanales'];
    protected $rules = [
        'idAlumno' => 'sometimes|required',
        'idColaboracion' => 'sometimes|required',
        'idInstructor' => 'sometimes|required',
        'asociacion' => 'required',
        'desde' => 'sometimes|required|date',
        'hasta' => 'sometimes|required|date',
        'horas' => 'required|numeric',
    ];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'idInstructor' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'correoAlumno' => ['type' => 'hidden'],
        'correoInstructor' => ['type' => 'hidden'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    public function __construct()
    {
        $this->asociacion = 1;
        $this->horas = 400;
    }
    
//    public function Alumno()
//    {
//        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
//    }
    
    public function Colaboracion()
    {
        return $this->belongsTo(Colaboracion::class, 'idColaboracion', 'id');
    }
    public function Instructor()
    {
        return $this->belongsTo(Instructor::class, 'idInstructor', 'dni');
    }
    public function Colaboradores()
    {
        return $this->belongsToMany(Instructor::class,'colaboradores', 'idFct', 'idInstructor','id','dni')->withPivot('horas');
    }
    public function Alumnos()
    {
        return $this->belongsToMany(Alumno::class,'alumno_fcts', 'idFct', 'idAlumno','id','nia')->withPivot(['calificacion','calProyecto','actas','insercion','horas','desde','hasta']);
    }
    
    public function scopeCentro($query, $centro)
    {
        $colaboracion = Colaboracion::select('id')->where('idCentro',$centro)->get()->toarray();
        return $query->whereIn('idColaboracion', $colaboracion);
    }
    
    public function scopeEmpresa($query, $empresa)
    {
        $centros = Centro::select('id')->Empresa($empresa)->get()->toarray();
        $colaboracion = Colaboracion::select('id')->whereIn('idCentro',$centros)->get()->toarray();
        return $query->whereIn('idColaboracion', $colaboracion);
    }
    
    public function scopeMisFcts($query,$profesor=null,$dual=false)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor,$dual)->get()->toArray();
        $alumnos_fct = AlumnoFct::select('idFct')->distinct()->whereIn('idAlumno',$alumnos)->get()->toArray();
        return $query->whereIn('id',$alumnos_fct);
    }
    
    public function scopeGrupo($query,$grupo)
    {
        $alumnos = Alumno::select('nia')->misAlumnos($grupo->tutor)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos);
    }
    public function scopeEsFct($query)
    {
        return $query->where('asociacion','<',3);
    }
    public function scopeEsDual($query)
    {
        return $query->where('asociacion',3);
    }
    public function scopeActiva($query,$cuando)
    {
        $hoy = new Date();
        $hoy->format('Y-m-d');
        if ($hoy <= config('curso.fct.1')['fi'])
            return $query->where('desde','<=',config('curso.fct.1')['fi'])->where('asociacion',1);
        if ($hoy < config('curso.fct.2')['inici'] &&($cuando == 3)) 
            return $query->where('desde','<=',config('curso.fct.1')['fi'])->where('asociacion',1);  
        return $query->where('desde','>=',config('curso.fct.2')['inici'])->where('asociacion',1);
           
    }
   
    public function getIdColaboracionOptions(){
        $cicloC = Grupo::select('idCiclo')->QTutor(AuthUser()->dni)->get();
        $ciclo = $cicloC->count()>0?$cicloC->first()->idCiclo:'';
        $colaboraciones = Colaboracion::where('idCiclo',$ciclo)->get();
        $todos = [];
        
        foreach ($colaboraciones as $colaboracion){
            if ($colaboracion->Centro->Empresa->concierto){
                $todos[$colaboracion->id] = $colaboracion->Centro->nombre;
                if ($colaboracion->Centro->direccion) $todos[$colaboracion->id].=' ('.$colaboracion->Centro->direccion.')';
            }    
        }
        return array_sort($todos, function ($value) {
            return $value;
        });
    }
    public function getIdAlumnoOptions(){
        return hazArray(Alumno::misAlumnos()->orderBy('apellido1')->orderBy('apellido2')->get(),'nia',['NameFull','horasFct'],' - ');
    }
    public function getIdInstructorOptions(){
        if ($this->idColaboracion){
            $colaboracion = Colaboracion::find($this->idColaboracion);
           return hazArray($colaboracion->Centro->instructores,'dni',['nombre']);
        }
        else return [];
    }
    public function getTipusAttribute(){
        return config('auxiliares.asociacionEmpresa')[$this->asociacion];
    }
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getHastaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getCentroAttribute(){
        return isset($this->Colaboracion->Centro->nombre)?$this->Colaboracion->Centro->nombre:'Convalidada/Exent';
    }
    public function getPeriodeAttribute(){
        $inici = new Date($this->desde);
        $inici->format('Y-m-d');
        if ($inici <= config('curso.fct.2')['inici']) return 1;
        else return 2;    
    }

    
    public function getCicloAttribute(){
        return $this->Colaboracion->Ciclo->ciclo;
    }
    public function getQuantsAttribute(){
       $quants = 0;
        foreach ($this->Alumnos as $alumno){
            if (in_array(AuthUser(),$alumno->tutor)) $quants ++;
        } 
        return $quants;
    }
    public function getNalumnesAttribute(){
        if ($this->quants != $this->Alumnos->Count())
            return $this->quants.' ('.$this->Alumnos->Count().')';
        else
            return $this->quants;
    }
    public function getLalumnesAttribute(){
        $alumnes = '';
        foreach ($this->Alumnos as $alumno){
            if (in_array(AuthUser(),$alumno->tutor)) 
                $alumnes .= $alumno->ShortName.', ';
        } 
        return substr($alumnes,0, strlen($alumnes)-2);
    }
    public function getXInstructorAttribute(){
        return isset($this->idInstructor)?$this->Instructor->nombre:'Falta Instructor';
    }
}
