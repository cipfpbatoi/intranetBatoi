<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Services\Document\TipoReunionService;
use Illuminate\Support\Facades\App;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Events\ReunionCreated;
use Intranet\Presentation\Crud\ReunionCrudSchema;


/**
 * Model de reunions.
 */
class Reunion extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

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
    protected $rules = ReunionCrudSchema::RULES;
    protected $inputTypes = ReunionCrudSchema::INPUT_TYPES;
    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
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
        return $this->belongsToMany(
            Alumno::class,
            'alumno_reuniones',
            'idReunion',
            'idAlumno'
        )->withPivot('capacitats');
    }
    public function noPromocionan()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_reuniones', 'idReunion', 'idAlumno')->withPivot('capacitats')
            ->wherePivot('capacitats', 3);
    }
    public function Departament()
    {
        return $this->hasOneThrough(Departamento::class, Profesor::class, 'dni', 'id', 'idProfesor', 'departamento');
    }


    public function scopeMisReuniones($query)
    {
        $reuniones = Asistencia::query()
                ->whereIn('idProfesor', array_filter([authUser()->dni, authUser()->sustituye_a]))
                ->pluck('idReunion')
                ->all();
        return $query->whereIn('id', $reuniones)->orWhere('idProfesor', authUser()->dni);
    }
    public function scopeConvocante($query, $dni=null)
    {
        $dni = $dni??authUser()->dni;
        $sustituye = Profesor::query()->find($dni)?->sustituye_a;

        return $query->where(function ($innerQuery) use ($dni, $sustituye) {
            $innerQuery->where('idProfesor', $dni);
            if (!empty($sustituye)) {
                $innerQuery->orWhere('idProfesor', $sustituye);
            }
        });
    }
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    public function scopeNumero($query, $numero)
    {
        if ($numero > 0) {
            return $query->where('numero', $numero);
        } else {
            return $query;
        }
    }
    public function scopeArchivada($query)
    {
        return $query->where('archivada', 1);
    }
    public function scopeActaFinal($query, $tutor)
    {
        return $query->where('tipo', 7)->where('numero', 34)->where('idProfesor', $tutor);
    }

    public function getTipoOptions()
    {
        return TipoReunionService::allSelect();
    }

    public function getIdEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    public function getNumeroOptions()
    {
        if (isset($this->tipo)) {
            return $this->Tipos()->numeracion;
        } else {
            return config('auxiliares.numeracion');
        }
    }

    public function getGrupoOptions()
    {
        return hazArray(GrupoTrabajo::MisGruposTrabajo()->get(), 'id', 'literal');
    }

    public function getDepartamentoAttribute()
    {
        return $this->Departament->literal ?? '';
    }
    public function getAvaluacioAttribute()
    {
        return $this->numero-20;
    }
    public function getModificableAttribute()
    {
        return $this->Tipos()->modificable;
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
        return new TipoReunionService($this->tipo);
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
        if ($this->grupo) {
            return $this->Grupos->literal ?? '';
        }
        $colectivo =$this->Tipos()->colectivo;
        $profesor = Profesor::query()->find($this->idProfesor);
        if (!$profesor) {
            return '';
        }
        $grupo = '';
        switch ($colectivo) {
            case 'Profesor':
                $grupo='Claustro';
                break;
            case 'Jefe':
                $grupo='COCOPE';
                break;
            case 'Departamento':
                $grupo = Departamento::query()->where('id', $profesor->departamento)->value('cliteral') ?? '';
                break;
            case 'Grupo':
                $grupo = app(GrupoService::class)->largestByTutor($profesor->dni)?->nombre ?? '';
                break;
            default:
                $grupo = '';
        }
        return $grupo;
    }

    public function getCicloAttribute()
    {
        return app(GrupoService::class)->firstByTutor($this->idProfesor)?->Ciclo->ciclo ?? '';
    }

    public function getXtipoAttribute()
    {
        $tr = $this->Tipos();
        return App::getLocale(session('lang')) == 'es'?$tr->cliteral:$tr->vliteral;
    }
    public function getXnumeroAttribute()
    {
        return config("auxiliares.numeracion.$this->numero");
    }
    public function getAvaluacioFinalAttribute()
    {
        return ($this->tipo == 7 && $this->numero == 34);
    }
    public function getExtraOrdinariaAttribute()
    {
        return ($this->tipo == 7 && $this->numero == 35);
    }
    public function getGrupoClaseAttribute()
    {
        return $this->Tipos()->colectivo == 'Grupo'
            ? app(GrupoService::class)->largestByTutor($this->idProfesor)
            : null;
    }
    public function getInformeAttribute()
    {
        if ($this->extraOrdinaria) {
            return true;
        }
        return false;
    }
    public function getIsSemiAttribute()
    {
        return (bool) ($this->GrupoClase->isSemi ?? false);
    }

    public function scopeNext($query)
    {
        $fecHoy = time();
        $ahora = date("Y-m-d", $fecHoy);
        return $query->where('fecha', '>', $ahora);
    }

}
