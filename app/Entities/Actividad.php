<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActividadCreated;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;


class Actividad extends Model
{

    use BatoiModels;

    protected $table = 'actividades';
    protected $fillable = ['name', 'extraescolar','desde', 'hasta','fueraCentro', 'transport','objetivos', 'descripcion', 'comentarios','poll','desenvolupament','valoracio','aspectes','dades'];
    protected $rules = [
        'name' => 'required|between:1,75',
        'desde' => 'required|date',
        'hasta' => 'required|date|after:desde',
    ];
    protected $inputTypes = [
        'id' => ['type' => 'hidden'],
        'objetivos' => ['type' => 'textarea'],
        'extraescolar' => ['type' => 'hidden'],
        'descripcion' => ['type' => 'textarea'],
        'comentarios' => ['type' => 'textarea'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        //'poll' => ['type' => 'checkbox'],
        'fueraCentro' => ['type' => 'checkbox'],
        'transport' => ['type' => 'checkbox'],

    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
        'created' => ActividadCreated::class,
    ];
    public $descriptionField = 'name';
    protected $hidden = ['created_at', 'updated_at'];
    protected $attributes = [ 'fueraCentro' => 1];

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class,'actividad_grupo', 'idActividad', 'idGrupo', 'id', 'codigo');
    }

    public function profesores()
    {
        return $this->belongsToMany(Profesor::class,'actividad_profesor', 'idActividad', 'idProfesor', 'id', 'dni')->withPivot('coordinador');
    }

    public function menores(){
        return $this->belongsToMany(Alumno::class,'autorizaciones', 'idActividad', 'idAlumno', 'id', 'nia')->withPivot('autorizado');
    }
    

    /**
     * Devueld el id del Coordinador  
     * Se utiliza para poder editar o borrar
     * @return string dni
     */
    public function Creador()
    {
        if (Actividad_profesor::select('idProfesor')
                        ->where('idActividad', $this->id)
                        ->where('coordinador', 1)
                        ->count()) {
            return Actividad_profesor::select('idProfesor')
                ->where('idActividad', $this->id)
                ->where('coordinador', 1)
                ->first()
                ->idProfesor;
        }
        return null;
    }
    public function scopeProfesor($query,$dni)
    {
        $actividades = Actividad_profesor::select('idActividad')->where('idProfesor',$dni)->get()->toArray();
        return $query->whereIn('id',$actividades);
    }

    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y H:i');
    }

    public function getHastaAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y H:i');
    }

    public function scopeNext($query)
    {
        $fec_hoy = time();
        $ahora = date("Y-m-d", $fec_hoy);
        return $query->where('desde', '>', $ahora);
    }

    public function scopeAuth($query)
    {
        return $query->where('estado','>=', 2)->orWhere('extraescolar',0);
    }

    public function scopeDia($query, $dia)
    {
        $antes = $dia . " 23:59:59";
        $despues = $dia . " 00:00:00";
        return $query->where('desde', '<=', $antes)
                        ->where('hasta', '>=', $despues);
    }

    public function scopeDepartamento($query, $dep)
    {
        $actividades = ActividadGrupo::select('idActividad')->Departamento($dep)->get()->toarray();
        return $query->whereIn('id', $actividades);
    }

    public function Tutor()
    {
        return $this->belongsToMany(Profesor::class, 'actividad_profesor', 'idActividad', 'idProfesor')->wherePivot('coordinador', 1);
    }
    public function getcoordAttribute()
    {
        return ($this->Creador() === AuthUser()->dni)?1:0;
    }
    public function getsituacionAttribute()
    {
        return trans('models.Actividad.' . $this->estado);
    }

    public static function loadPoll(){
        $actividades = collect();
        foreach (AuthUser()->Grupo as $grupo) {
            foreach ($grupo->Actividades as $actividad) {
                $actividades->push(['option1' => $actividad]);
            }
        }
        return $actividades;
    }

    public function getRecomendadaAttribute(){
        return $this->recomanada?'Sí':'No';
    }

}
