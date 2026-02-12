<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActividadCreated;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;


/**
 * Model d'activitats extraescolars/complementàries.
 *
 * Inclou relacions amb grups, professors i autoritzacions de menors,
 * i accessors de presentació per a dates i situació.
 */
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

    /**
     * Accessor de `desde` en format de visualització.
     *
     * @param string|null $entrada
     * @return string|null
     */
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y H:i');
    }

    /**
     * Accessor de `hasta` en format de visualització.
     *
     * @param string|null $salida
     * @return string|null
     */
    public function getHastaAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y H:i');
    }

    /**
     * Filtra activitats futures.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNext($query)
    {
        $today = time();
        $ahora = date("Y-m-d", $today);
        return $query->where('desde', '>', $ahora);
    }

    /**
     * Filtra activitats autoritzades o no extraescolars.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuth($query)
    {
        return $query->where(function ($q) {
            $q->where('estado', '>=', 2)
                ->orWhere('extraescolar', 0);
        });
    }

    /**
     * Filtra activitats que cauen en un dia concret.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dia Format Y-m-d
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDia($query, $dia)
    {
        $antes = $dia . " 23:59:59";
        $despues = $dia . " 00:00:00";
        return $query->where('desde', '<=', $antes)
                        ->where('hasta', '>=', $despues);
    }

    /**
     * Filtra per departament a través dels grups de l'activitat.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $dep
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDepartamento($query, $dep)
    {
        $actividades = ActividadGrupo::select('idActividad')->Departamento($dep)->get()->toarray();
        return $query->whereIn('id', $actividades);
    }

    /**
     * Relació de professor coordinador de l'activitat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Tutor()
    {
        return $this->belongsToMany(
            Profesor::class,
            'actividad_profesor',
            'idActividad',
            'idProfesor'
        )->wherePivot('coordinador', 1);
    }
    /**
     * Accessor booleà per saber si l'usuari autenticat és coordinador.
     *
     * @return int
     */
    public function getcoordAttribute()
    {
        return ($this->Creador() === authUser()->dni)?1:0;
    }
    /**
     * Accessor de text de situació segons estat.
     *
     * @return string
     */
    public function getsituacionAttribute()
    {
        return trans('models.Actividad.' . $this->estado);
    }

    /**
     * Carrega activitats de poll dels grups de l'usuari.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function loadPoll()
    {
        return authUser()->Grupo->flatMap(fn($grupo) =>
        $grupo->Actividades->map(fn($actividad) => ['option1' => $actividad])
        );
    }

    /**
     * Accessor de "recomanada" en format Sí/No.
     *
     * @return string
     */
    public function getRecomendadaAttribute()
    {
        return $this->recomanada?'Sí':'No';
    }

    /**
     * Opcions de tipus d'activitat segons departament de l'usuari.
     *
     * @return array
     */
    public function getTipoActividadIdOptions()
    {
        return hazArray(
            TipoActividad::where('departamento_id', authUser()->departamento)->orderBy('vliteral')->get(),
            'id',
            'vliteral'
        );
    }

}
