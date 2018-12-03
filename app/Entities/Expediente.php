<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo;

class Expediente extends Model
{

    use BatoiModels,TraitEstado;

    public $timestamps = false;
    protected $visible = [
        'id',
        'tipo',
        'idModulo',
        'idAlumno',
        'idProfesor',
        'estado',
        'fecha',
        'fechasolucion',
        'explicacion',
    ];
    protected $fillable = [
        'tipo',
        'idModulo',
        'idAlumno',
        'idProfesor',
        'explicacion',
        'fecha',
    ];
    protected $rules = [
        'fecha' => 'required',
        'explicacion' => 'required'
    ];
    protected $inputTypes = [
        'tipo' => ['type' => 'select'],
        'idModulo' => ['type'=>'select'],
        'idAlumno' => ['type' => 'select'],
        'idProfesor' => ['type' => 'hidden'],
        'explicacion' => ['type' => 'textarea'],
        'fecha' => ['type' => 'date'],
    ];
    protected $dispatchesEvents = [
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function __construct()
    {
        if (AuthUser())
            $this->idProfesor = AuthUser()->dni;
    }
    
    public function tipoExpediente(){
        return $this->belongsTo(TipoExpediente::class,'tipo','id');
    }
    
    public function getfechaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getfechasolucionAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y');
    }

    public function getTipoOptions()
    {
        return hazArrayRole(TipoExpediente::all(),'id','titulo');
    }
    
    public function getIdModuloOptions(){
        return hazArray(Modulo::MisModulos()->Lectivos()->get(),'codigo', 'vliteral');
       // return hazArray(Modulo::ModulosGrupo(Grupo::Qtutor()->first()->codigo)->Lectivos()->get(),'codigo', 'vliteral');
    }

    public function getIdAlumnoOptions()
    {
        $misAlumnos = [];
        $migrupo = Grupo::Qtutor()->get();
        if (isset($migrupo->first()->codigo)) {
            $alumnos = Alumno_grupo::where('idGrupo', '=', $migrupo->first()->codigo)->get();

            foreach ($alumnos as $alumno) {
                $misAlumnos[$alumno->idAlumno] = $alumno->Alumno->apellido1 . ' ' . $alumno->Alumno->apellido2 . ', ' . $alumno->Alumno->nombre;
            }
        }
        return $misAlumnos;
    }

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Modulo()
    {
        return $this->belongsTo(Modulo::class, 'idModulo', 'codigo');
    }
    public function getNomAlumAttribute(){
        return $this->Alumno->FullName;
    }
    public function getSituacionAttribute(){
        return isblankTrans('models.Expediente.'.$this->estado) ? trans('messages.situations.'.$this->estado) : trans('models.Expediente.' . $this->estado);
    }
    public function getXtipoAttribute(){
        return $this->tipoExpediente->titulo;
    }
    public function getXmoduloAttribute(){
        return isset($this->Modulo->cliteral)?$this->Modulo->literal:'';
    }
    public function getShortAttribute(){
        return substr($this->explicacion,0,40);
    }

}
