<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Poll\Vote;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Events\FctCreated;
use Illuminate\Support\Arr;



class Fct extends Model
{
    use BatoiModels;

    
    protected $table = 'fcts';
    public $timestamps = false;

    protected $fillable = ['idAlumno',
        'idColaboracion','idInstructor','periode' ,'desde', 'hasta',
        'horas','asociacion'
        ];
    protected $notFillable = ['desde','hasta','idAlumno','horas'];

    protected $rules = [
        'idAlumno' => 'sometimes|required',
        'idColaboracion' => 'sometimes|required',
        'idInstructor' => 'sometimes|required',
        'periode' => 'required',
        'desde' => 'sometimes|required|date',
        'hasta' => 'sometimes|required|date',
    ];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'idInstructor' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'periode' => ['type' => 'select'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],

        
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'created' => FctCreated::class,
        'deleted' => ActivityReport::class,
    ];
    protected $attributes  = ['asociacion'=>1];


    public function Comision()
    {
        return $this->belongsToMany(Comision::class,'comision_fcts', 'idFct', 'idComision','id','id')->withPivot(['hora_ini','aviso']);
    }
    
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
        return $this->belongsToMany(Alumno::class,'alumno_fcts', 'idFct', 'idAlumno','id','nia')->withPivot(['calificacion','calProyecto','actas','insercion','horas','desde','hasta','correoAlumno','pg0301']);
    }
    public function AlFct()
    {
        return $this->hasMany(AlumnoFct::class,'idFct');
    }
    public function votes()
    {
        return $this->hasMany(Vote::class,'idOption1');
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
        $profesor = $profesor??authUser()->dni;
        $cicloC =  Grupo::QTutor($profesor,$dual)->first()->idCiclo??null;

        $colaboraciones = Colaboracion::select('id')->where('idCiclo',$cicloC)->get()->toArray();

        $alumnos = Alumno::select('nia')->misAlumnos($profesor,$dual)->get()->toArray();
        $alumnos_fct = AlumnoFct::select('idFct')->distinct()->whereIn('idAlumno',$alumnos)->get()->toArray();

        return $query->whereIn('id',$alumnos_fct)->whereIn('idColaboracion',$colaboraciones);
    }


    public function scopeMisFctsColaboracion($query,$profesor=null)
    {
        $dni = $profesor??authUser()->dni;
        $colaboraciones = hazArray(Colaboracion::where('tutor',$dni)->get(),'id','id');
        return $query->whereIn('idColaboracion',$colaboraciones);
    }

    public function scopeEsExempt($query)
    {
        return $query->where('asociacion','=',2);
    }
    
    public function scopeEsFct($query)
    {
        return $query->where('asociacion','<',2);
    }
    public function scopeEsAval($query)
    {
        return $query->where('asociacion','<',3);
    }
    public function scopeEsDual($query)
    {
        return $query->where('asociacion',3);
    }

    public function scopeNoAval($query)
    {
        return $query->where('correoInstructor',0);
    }
   
    public function getPeriodeOptions(){
        return config('auxiliares.periodesFct');
    }
   
    public function getIdColaboracionOptions(){
        $cicloC = Grupo::select('idCiclo')->QTutor(authUser()->dni)->get();
        $ciclo = $cicloC->count()>0?$cicloC->first()->idCiclo:'';
        $colaboraciones = Colaboracion::where('idCiclo',$ciclo)->get();
        $todos = [];
        
        foreach ($colaboraciones as $colaboracion){
            if ($colaboracion->Centro->Empresa->concierto){
                $todos[$colaboracion->id] = $colaboracion->Centro->nombre;
                if ($colaboracion->Centro->direccion) {
                    $todos[$colaboracion->id] .= ' (' . $colaboracion->Centro->direccion . ')';
                }
            }
        }
        return Arr::sort($todos, function ($value) {
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
        return [];
    }

    public function getTipusAttribute(){
        return config('auxiliares.asociacionEmpresa')[$this->asociacion];
    }
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getDualAttribute()
    {
        return $this->asociacion == 3;
    }
    public function getExentoAttribute()
    {
        return $this->asociacion == 2;
    }
    
    public function getCentroAttribute(){
        return isset($this->Colaboracion->Centro->nombre)?$this->Colaboracion->Centro->nombre:'Convalidada/Exent';
    }



    public function getinTimeAttribute(){
        $hoy = hoy('Y-m-d');
        if ( $hoy > config('curso.fct.2')['inici'] ){
            return ($this->periode == 2);
        } else {
            return ($this->periode == 1);
        }
    }

    public function getCicloAttribute(){
        return $this->Colaboracion->Ciclo->ciclo;
    }
    public function getQuantsAttribute(){
       $quants = 0;
        foreach ($this->Alumnos as $alumno){
            if (in_array(authUser(),$alumno->tutor)) {
                $quants++;
            }
        } 
        return $quants;
    }
    public function getNalumnesAttribute(){
        if ($this->quants != $this->Alumnos->Count()) {
            return $this->quants . ' (' . $this->Alumnos->Count() . ')';
        }
        return $this->quants;
    }

    public function getLalumnesAttribute(){
        $alumnes = '';
        foreach ($this->Alumnos as $alumno){
            if (in_array(authUser(),$alumno->tutor)) {
                $alumnes .= $alumno->ShortName . ', ';
            }
        } 
        return substr($alumnes,0, strlen($alumnes)-2);
    }
    public function getEmailAttribute(){
        return isset($this->idInstructor)?$this->Instructor->email:'Falta Instructor';
    }
    public function getContactoAttribute(){
        return isset($this->idInstructor)?$this->Instructor->nombre:'Falta Instructor';
    }

    public function getXinstructorAttribute(){
        if (isset($this->Instructor->nombre)) {
            return $this->Instructor->nombre;
        }
        return '';
    }
    public function saveContact($contacto,$email)
    {
        $instructor = $this->Instructor;
        $instructor->email = $email;
        $instructor->name = '';
        $instructor->surnames = $contacto;
        $instructor->save();
    }

}
