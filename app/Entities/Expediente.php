<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Events\ActivityReport;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Modulo;
use Intranet\Presentation\Crud\ExpedienteCrudSchema;

class Expediente extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    public $timestamps = false;


    protected $fillable = [
        'tipo',
        'idModulo',
        'idAlumno',
        'idProfesor',
        'explicacion',
        'fecha',
        'fechatramite'
    ];
    protected $inputTypes = ExpedienteCrudSchema::INPUT_TYPES;
    protected $dispatchesEvents = [
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];


    
    public function tipoExpediente()
    {
        return $this->belongsTo(TipoExpediente::class, 'tipo', 'id');
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
    
    public function getfechatramiteAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    




    public function getTipoOptions()
    {
        $array = [];
        foreach (TipoExpediente::all() as $tipo) {
            if (esRol(AuthUser()->rol, $tipo->rol)) {
                $array[$tipo->id] = $tipo->titulo;
            }
        }
        return $array;
    }
    
    public function getIdModuloOptions()
    {
        return hazArray(Modulo::MisModulos()->Lectivos()->get(), 'codigo', 'vliteral');
    }

    public function getIdAlumnoOptions()
    {
        $misAlumnos = [];
        $migrupos = app(GrupoService::class)->misGrupos();
        foreach ($migrupos as $migrupo) {
            if (isset($migrupo->codigo)) {
                $alumnos = AlumnoGrupo::where('idGrupo', '=', $migrupo->codigo)->get();

                foreach ($alumnos as $alumno) {
                    $misAlumnos[$alumno->idAlumno] = $alumno->Alumno->nameFull;
                }
            }
        }
        asort($misAlumnos);
        return $misAlumnos;
    }

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
    public function Acompanyant()
    {
        return $this->belongsTo(Profesor::class, 'idAcompanyant', 'dni');
    }

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Modulo()
    {
        return $this->belongsTo(Modulo::class, 'idModulo', 'codigo');
    }
    public function getNomAlumAttribute()
    {
        return $this->Alumno->FullName;
    }

    public function getNomProfeAttribute()
    {
        return $this->Profesor->FullName;
    }
    public function getSituacionAttribute()
    {
        return isblankTrans('models.Expediente.'.$this->estado)
            ? trans('messages.situations.'.$this->estado)
            : trans('models.Expediente.' . $this->estado);
    }
    public function getXtipoAttribute()
    {
        return $this->tipoExpediente->titulo;
    }
    public function getXmoduloAttribute()
    {
        return $this->Modulo->literal??'';
    }
    public function getShortAttribute()
    {
        return substr($this->explicacion, 0, 40);
    }
    public function getEsInformeAttribute()
    {
        return $this->tipoExpediente->informe;
    }
    public function getQuienAttribute()
    {
        return $this->nomAlum;
    }
    public function scopeListos($query)
    {
        return $query->where('estado', 2);
    }

    public function getAnnexoAttribute()
    {
        return $this->tipoExpediente->orientacion;
    }

}
