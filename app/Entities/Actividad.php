<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActividadCreated;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;


class Actividad extends Model
{

    use BatoiModels ;

    protected $table = 'actividades';
    protected $fillable = [
        'name',
        'tipo_actividad_id',
        'extraescolar',
        'desde',
        'hasta',
        'complementaria',
        'fueraCentro',
        'transport',
        'objetivos',
        'descripcion',
        'comentarios',
        'poll',
        'desenvolupament',
        'valoracio',
        'aspectes',
        'dades',

    ];
    protected $rules = [
        'name' => 'required|between:1,75',
        'desde' => 'required|date',
        'hasta' => 'required|date|after:desde',
    ];
    protected $inputTypes = [
        'id' => ['type' => 'hidden'],
        'tipo_actividad_id' => ['type' => 'select'],
        'objetivos' => ['type' => 'textarea'],
        'extraescolar' => ['type' => 'hidden'],
        'descripcion' => ['type' => 'textarea'],
        'comentarios' => ['type' => 'textarea'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        //'poll' => ['type' => 'checkbox'],
        'fueraCentro' => ['type' => 'checkbox'],
        'transport' => ['type' => 'checkbox'],
        'complementaria' => ['type' => 'checkbox'],

    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'deleted' => ActivityReport::class,
        'created' => ActividadCreated::class,
    ];
    public $descriptionField = 'name';
    protected $hidden = ['created_at', 'updated_at'];
    protected $attributes = [ 'fueraCentro' => 1];

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'actividad_grupo', 'idActividad', 'idGrupo', 'id', 'codigo');
    }

    public function profesores()
    {
        return $this->belongsToMany(
            Profesor::class,
            'actividad_profesor',
            'idActividad',
            'idProfesor',
            'id',
            'dni'
        )->withPivot('coordinador');
    }

    public function menores()
    {
        return $this->belongsToMany(
            Alumno::class,
            'autorizaciones',
            'idActividad',
            'idAlumno',
            'id',
            'nia'
        )->withPivot('autorizado');
    }

    public function tipoActividad()
    {
        return $this->belongsTo(TipoActividad::class, 'tipo_actividad_id');
    }
    

    /**
     * Devueld el id del Coordinador
     * Se utiliza para poder editar o borrar
     * @return string dni
     */
    public function Creador()
    {
        return ActividadProfesor::where([
            ['idActividad', $this->id],
            ['coordinador', 1]
        ])->value('idProfesor');
    }
    public function scopeProfesor($query, $dni)
    {
        $actividades = ActividadProfesor::where('idProfesor', $dni)
            ->pluck('idActividad')
            ->toArray();
        return $query->whereIn('id', $actividades);
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
        $today = time();
        $ahora = date("Y-m-d", $today);
        return $query->where('desde', '>', $ahora);
    }

    public function scopeAuth($query)
    {
        return $query->where('estado', '>=', 2)->orWhere('extraescolar', 0);
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
        return $this->belongsToMany(
            Profesor::class,
            'actividad_profesor',
            'idActividad',
            'idProfesor'
        )->wherePivot('coordinador', 1);
    }
    public function getcoordAttribute()
    {
        return ($this->Creador() === authUser()->dni)?1:0;
    }
    public function getsituacionAttribute()
    {
        return trans('models.Actividad.' . $this->estado);
    }

    public static function loadPoll()
    {
        return authUser()->Grupo->flatMap(fn($grupo) =>
        $grupo->Actividades->map(fn($actividad) => ['option1' => $actividad])
        );
    }

    public function getRecomendadaAttribute()
    {
        return $this->recomanada?'Sí':'No';
    }

    public function getTipoActividadIdOptions()
    {
        return hazArray(
            TipoActividad::where('departamento_id', authUser()->departamento)->orderBy('vliteral')->get(),
            'id',
            'vliteral'
        );
    }

}
