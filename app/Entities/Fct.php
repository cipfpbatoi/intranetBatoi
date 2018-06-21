<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class Fct extends Model
{
    use BatoiModels;
    
    protected $table = 'fcts';
    public $timestamps = false;

    protected $fillable = ['idAlumno', 'idColaboracion', 'desde','hasta'
        ,'horas','asociacion','horas_semanales','calificacion','correoAlumno','correoInstructor'];
//    protected $fillable = ['idAlumno', 'idColaboracion',  'desde','hasta'
//        ,'horas','asociacion','horas_semanales'];
    protected $rules = [
        'idAlumno' => 'required',
        'idColaboracion' => 'sometimes|required',
        'asociacion' => 'required',
        'desde' => 'required|date',
        'horas' => 'required|numeric',
    ];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'asociacion' => ['type' => 'select'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'calificacion' => ['type' => 'hidden'],
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
        $this->horas_semanales = 40;
        $this->horas = 400;
    }
    
    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Colaboracion()
    {
        return $this->belongsTo(Colaboracion::class, 'idColaboracion', 'id');
    }
//    public function Instructor()
//    {
//        return $this->belongsTo(Instructor::class, 'idInstructor', 'dni');
//    }
    public function Instructores()
    {
        return $this->belongsToMany(Instructor::class,'instructor_fcts', 'idFct', 'idInstructor','id','dni')->withPivot('horas');
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
    public function scopeMisFcts($query,$profesor=null)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos);
    }
    public function scopeGrupo($query,$grupo)
    {
        $alumnos = Alumno::select('nia')->misAlumnos($grupo->tutor)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos);
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
    
    
    public function scopeNoAval($query)
    {
        return $query->where('actas','<', 2);
    }
    public function scopePendiente($query)
    {
        return $query->where('actas','=', 3);
    }
    public function scopeAval($query)
    {
        return $query->where('actas','=', 2);
    }
    
    
    public function getAsociacionOptions()
    {
        return config('constants.asociacionEmpresa');
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
        return hazArray(Alumno::misAlumnos()->get(),'nia',['apellido1','apellido2','nombre']);
    }
    public function getIdInstructorOptions(){
        if ($this->idColaboracion){
            $colaboracion = Colaboracion::find($this->idColaboracion);
           return hazArray($colaboracion->Centro->instructores,'dni',['nombre']);
        }
        else return [];
    }
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y');
    }
    public function getNombreAttribute(){
        return $this->Alumno->FullName;
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
    public function getHoras_semanalesAttribute(){
        return $this->horas_semanales ? $this->horas_semanales : 40;
    }
    public function getFinAttribute(){
        $inicio = new Date($this->desde);
        $semanas = ($this->horas / $this->horas_semanales) * 1.1;
        return $inicio->addWeeks($semanas)->format('d-m-Y');
    }
    public function getQualificacioAttribute(){
        return isset($this->calificacion) ? $this->calificacion ?$this->calificacion==2?'Convalidat/Exempt': 'Apte' : 'No Apte' : 'No Avaluat';
    }
    public function getProjecteAttribute(){
        return isset($this->calProyecto) ? $this->calProyecto == 0 ? 'No presenta' : $this->calProyecto : 'No Avaluat';
    }
           
    public function getXInstructorAttribute(){
        $nombre = '';
        foreach ($this->Instructores as $instructor){
            $nombre .= $instructor->nombre.',';
        }
        return substr($nombre, 0, strlen($nombre)-1);
    }
    public function getTutorAttribute(){
        //dd($this->Alumno->Grupo->first()->Tutor->FullName);
        return isset($this->Alumno->Grupo->first()->Tutor->FullName)?$this->Alumno->Grupo->first()->Tutor->FullName:'';
    }
    public function getCicloAttribute(){
        return $this->Colaboracion->Ciclo->ciclo;
    }
}
