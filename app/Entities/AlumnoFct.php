<?php

namespace Intranet\Entities;

use Intranet\Application\AlumnoFct\AlumnoFctSignatureService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Intranet\Events\FctAlDeleted;
use Intranet\Presentation\AlumnoFct\AlumnoFctPresenter;


class AlumnoFct extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $fillable = ['id', 'desde', 'hasta', 'horas', 'beca', 'autorizacion', 'flexible', 'valoracio'];

    protected $rules = [
        'id' => 'required',
        'desde' => 'date',
        'hasta' => 'date',
        'horas' => 'required|numeric'
    ];

    protected $inputTypes = [
        'id' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'beca' => ['type' => 'hidden'],
        'autorizacion' => ['type' => 'checkbox'],
        'flexible' => ['type' => 'checkbox'],
        'valoracio' => ['type' => 'textarea']
    ];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'deleted' => FctAlDeleted::class,
    ];

    private ?\Illuminate\Support\Collection $annexesCache = null;

    // ===========================
    // 📌 RELACIONS
    // ===========================
    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }

    public function Fct()
    {
        return $this->belongsTo(Fct::class, 'idFct', 'id');
    }

    public function Dual()
    {
        return $this->belongsTo(Dual::class, 'idFct', 'id');
    }

    public function Signatures()
    {
        return $this->hasMany(Signatura::class, 'idSao', 'idSao');
    }

    public function Tutor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Contactos()
    {
        return $this->hasMany(Activity::class, 'model_id', 'id')
            ->mail()
            ->where('model_class', self::class);
    }


     

    // ===========================
    // 📌 SCOPES
    // ===========================
    public function scopeMisFcts($query, $profesor = null)
    {
        $profesor = Profesor::getSubstituts($profesor ?? authUser()->dni);
        return $query->whereIn('idProfesor', $profesor) ;
    }

    public function scopeTotesFcts($query, $profesor = null)
    {
        return $query->misFcts($profesor);
    }

    public function scopeMisProyectos($query, $profesor = null)
    {
        return $query->misFcts($profesor)
            ->esAval()
            ->whereNull('calProyecto');
    }

    public function scopeEsFct($query)
    {
        return $query->whereIn('idFct', Fct::select('id')->esFct()->pluck('id'));
    }

    public function scopeEsAval($query)
    {
        return $query->whereIn('idFct', Fct::select('id')->esAval()->pluck('id'));
    }

    public function scopeEsDual($query)
    {
        return $query->whereIn('idFct', Fct::select('id')->esDual()->pluck('id'));
    }

    public function scopeMisDual($query, $profesor=null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esDual();
    }
    
    public function scopeMisConvalidados($query, $profesor=null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esExempt();
    }

    public function scopeNoAval($query)
    {
        return $query->where('actas', '<', 2);
    }

    public function scopePendiente($query)
    {
        return $query->where('actas', '=', 3);
    }

    public function scopeAval($query)
    {
        return $query->where('actas', '=', 2);
    }

    public function scopePendienteNotificar($query)
    {
        return $query->where('calificacion', 1)
            ->where('correoAlumno', 0);
    }

    public function scopeCalificados($query)
    {
        return $query->whereNotNull('calificacion');
    }

    public function scopeAprobados($query)
    {
        return $query->where('calificacion', 1);
    }

    public function scopeTitulan($query)
    {
        return $query->where('calificacion', '>', 0)->where('calProyecto', '>', 4);
    }

    public function scopeRealFcts($query, $profesor = null)
    {
        $profesor = Profesor::getSubstituts($profesor ?? authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->estaSao();
    }

    public function scopeAvaluables($query, $profesor = null)
    {
        $profesor = Profesor::getSubstituts($profesor ?? authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esAval();
    }

    public function scopeMisErasmus($query, $profesor = null)
    {
        $profesor = Profesor::getSubstituts($profesor ?? authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esErasmus();
    }


    public function scopeEsErasmus($query)
    {
        $fcts = Fct::select('id')->esErasmus()->pluck('id');
        return $query->whereIn('idFct', $fcts);
    }

    public function scopeEsExempt($query)
    {
        $fcts = Fct::select('id')->esExempt()->pluck('id');
        return $query->whereIn('idFct', $fcts);
    }

    public function scopeEstaSao($query)
    {
        $fcts = Fct::select('id')->esExempt()->pluck('id');
        return $query->whereNotIn('idFct', $fcts);
    }

    public function scopeActiva($query)
    {
       return $query->whereNull('calificacion')->where('correoAlumno', 0)->whereColumn('horas', '>', 'realizadas');
    }

    public function scopeHaEmpezado($query)
    {
        return $query->where('desde', '<=', Hoy('Y-m-d'));
    }

    public function scopeNoHaAcabado($query)
    {
        return $query->where('hasta', '>=', Hoy('Y-m-d'));
    }

    // ===========================
    // 📌 GETTERS D'ATRIBUTS
    // ===========================
    public function getEmailAttribute()
    {
        return $this->Alumno?->email ?? null;
    }

    public function getCentroAttribute()
    {
        return $this->presenter()->centerName(30);
    }


    public function getNombreAttribute()
    {
        return $this->presenter()->studentShortName();
    }

    public function getNomEdatAttribute()
    {
        return $this->presenter()->studentNameWithMinorIcon();
    }

    public function getQualificacioAttribute()
    {
        return match ($this->calificacion) {
            0 => 'No Apte',
            1 => 'Apte',
            2 => 'Convalidat/Exempt',
            default => 'No Avaluat',
        };
    }



    public function getDesdeAttribute($entrada)
    {
        return (new Date($entrada))->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        return $this->getDesdeAttribute($entrada);
    }

    public function getFinPracticasAttribute()
    {
        return $this->presenter()->remainingPracticeTimeLabel();
    }

    public function getClassAttribute()
    {
        return $this->presenter()->cssClass();
    }

    public function presenter(): AlumnoFctPresenter
    {
        return new AlumnoFctPresenter($this);
    }

    public function getAdjuntosAttribute()
    {
        return $this->getAnnexesCollection()->isNotEmpty();
    }

    public function routeFile($anexe)
    {
        return $this->signatureService()->routeFile($this, (string) $anexe);
    }

    public function getSignAttribute()
    {
        return $this->signatureService()->hasAnySignature($this);
    }
    

    public function getContactoAttribute()
    {
        return $this->presenter()->contactName();
    }

    public function getFullNameAttribute()
    {
        return $this->presenter()->fullName();
    }
    public function getHorasRealizadasAttribute()
    {
        return $this->presenter()->completedHoursLabel();
    }

    public function getHorasTotalAttribute()
    {
        return $this->correoAlumno
            ? $this->horas
            : static::query()
                ->where('idAlumno', $this->idAlumno)
                ->where('correoAlumno', 0)
                ->sum('horas');
    }

    public function getPeriodeAttribute()
    {
        return $this->Fct->periode;
    }


    public function getProjecteAttribute()
    {
       return match($this->calProyecto){
            0 =>  'No presenta' ,
            null => 'No Avaluat',
            default => $this->calProyecto,
        };

    }
    public function getAsociacionAttribute()
    {
        return $this->Fct->asociacion;
    }

    public function getMiniCentroAttribute()
    {
        return $this->presenter()->centerName(15);
    }
    public function getInstructorAttribute()
    {
        return $this->presenter()->instructorName(30);
    }
    

    public function getGrupAttribute()
    {
        foreach ($this->Alumno->Grupo as $grupo) {
            if ($grupo->Ciclo == $this->Fct->Colaboracion->Ciclo) {
                return $grupo->codigo;
            }
        }
        return null;
    }
    public function scopeGrupo($query, $grupo)
    {
        $codigo = $grupo instanceof Grupo ? $grupo->codigo : $grupo;
        $table = $query->getModel()->getTable();

        return $query->whereExists(function ($sub) use ($codigo, $table) {
            $sub->select(DB::raw(1))
                ->from('alumnos_grupos as ag')
                ->join('grupos as g', 'g.codigo', '=', 'ag.idGrupo')
                ->join('fcts as f', 'f.id', '=', $table . '.idFct')
                ->join('colaboraciones as c', 'c.id', '=', 'f.idColaboracion')
                ->where('g.codigo', $codigo)
                ->whereColumn('ag.idAlumno', $table . '.idAlumno')
                ->whereColumn('g.idCiclo', 'c.idCiclo');
        });
    }

    public function getQuienAttribute()
    {
        return $this->fullName;
    }

    public function getSaoAnnexesAttribute()
    {
        return $this->getAnnexesCollection()->where('size', 1024)->count();
    }

    public function getA2Attribute()
    {
        return $this->signatureService()->findByType($this, 'A2', true);
    }

    public function getA1Attribute()
    {
        return $this->signatureService()->findByType($this, 'A1', true);
    }

    public function getA3Attribute()
    {
        return $this->signatureService()->findByType($this, 'A3');
    }



    public function getIdPrintAttribute()
    {
        return $this->presenter()->printableId();
    }

    private function getAnnexesCollection(): \Illuminate\Support\Collection
    {
        if ($this->annexesCache !== null) {
            return $this->annexesCache;
        }

        $this->annexesCache = Adjunto::where('route', 'alumnofctaval/' . $this->id)->get();

        return $this->annexesCache;
    }

    private function signatureService(): AlumnoFctSignatureService
    {
        return app(AlumnoFctSignatureService::class);
    }




}
