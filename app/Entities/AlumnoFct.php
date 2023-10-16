<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\FctAlDeleted;


class AlumnoFct extends Model
{

    use BatoiModels;
    protected $fillable = ['id', 'desde','hasta','horas','beca','autorizacion'];
    
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
        'autorizacion' => ['type' => 'checkbox']
    ];
    public $timestamps = false;
    protected $dispatchesEvents = [
        'deleting' => FctAlDeleted::class,
    ];
    
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

    public function Contactos()
    {
        return $this->hasMany(Activity::class, 'model_id', 'id')
            ->mail()
            ->where('model_class', 'Intranet\Entities\AlumnoFct');
    }

    public function Signatures()
    {
        return $this->hasMany(Signatura::class, 'idSao', 'idSao');
    }


    /*
    public function scopeMisFcts($query, $profesor=null)
    {
        $profesor = $profesor?$profesor:authUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();

        $cicloC = Grupo::select('idCiclo')->QTutor($profesor)->first()->idCiclo;
        $colaboraciones = Colaboracion::select('id')->where('idCiclo', $cicloC)->get()->toArray();

        $fcts = Fct::select('id')
            ->whereIn('idColaboracion', $colaboraciones)
            ->where('asociacion', '<', 3)
            ->get()
            ->toArray();
        return $query->whereIn('idAlumno', $alumnos)->whereIn('idFct', $fcts);
    }
    */
    public function scopeMisFcts($query, $profesor=null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor);
    }


    public function scopeMisProyectos($query, $profesor=null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)
            ->esAval()
            ->whereNull('calProyecto');
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

    public function scopeEsFct($query)
    {
        $fcts = Fct::select('id')->esFct()->get()->toArray();
        return $query->whereIn('idFct', $fcts);
    }
    public function scopeEsAval($query)
    {
        $fcts = Fct::select('id')->esAval()->get()->toArray();
        return $query->whereIn('idFct', $fcts);
    }
    public function scopeEsDual($query)
    {
        $fcts = Fct::select('id')->esDual()->get()->toArray();
        return $query->whereIn('idFct', $fcts);
    }

    public function scopeEsErasmus($query)
    {
        $fcts = Fct::select('id')->esErasmus()->get()->toArray();
        return $query->whereIn('idFct', $fcts);
    }

    public function scopeEsExempt($query)
    {
        $fcts = Fct::select('id')->esExempt()->get()->toArray();
        return $query->whereIn('idFct', $fcts);
    }

    public function scopeActiva($query)
    {
       return $query->whereNull('calificacion')->where('correoAlumno', 0)->where('horas', '>', 'realizadas');
    }

    public function scopeHaEmpezado($query)
    {
        return $query->where('desde', '<', Hoy('Y-m-d'));
    }

    public function scopeNoHaAcabado($query)
    {
        return $query->where('hasta', '>', Hoy('Y-m-d'));
    }
    
    public function getEmailAttribute()
    {
        return $this->Alumno->email;
    }
    public function getContactoAttribute()
    {
        return $this->Alumno->NameFull;
    }
    public function getNombreAttribute()
    {
        return $this->Alumno->ShortName;
    }
    public function getFullNameAttribute()
    {
        return $this->Alumno->fullName;
    }
    public function getHorasRealizadasAttribute()
    {
        return $this->realizadas.'/'.$this->horas.' '.$this->actualizacion;
    }

    public function getFinPracticasAttribute()
    {
        if ($this->horas_diarias) {
            $dias = (int)($this->horas-$this->realizadas)/$this->horas_diarias;
            $semanas = floor($dias / 5);
            $dias = $dias % 5;
            return "{$semanas} Setmanes - {$dias} Dia";
        }
        return '??';
    }

    public function getPeriodeAttribute()
    {
        return $this->Fct->periode;
    }
    public function getQualificacioAttribute()
    {
       /* return isset($this->calificacion)?
            ($this->calificacion?
                ($this->calificacion==2?'Convalidat/Exempt': 'Apte')
                : 'No Apte')
            : 'No Avaluat';*/

        return match($this->calificacion) {
             0 => 'No Apte',
             1 => 'Apte' ,
             2 => 'Convalidat/Exempt',
             null =>  'No Avaluat',
         };
    }

    public function getProjecteAttribute()
    {
        /*return isset($this->calProyecto)?
            ($this->calProyecto == 0 ? 'No presenta' : $this->calProyecto)
            : 'No Avaluat';*/
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
    public function getCentroAttribute()
    {
        return substr($this->Fct->Centro, 0, 30);
    }
    public function getMiniCentroAttribute()
    {
        return substr($this->Fct->Centro, 0, 15);
    }
    public function getInstructorAttribute()
    {
        return substr($this->Fct->XInstructor, 0, 30);
    }
    
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getHastaAttribute($entrada)
    {
        return $this->getDesdeAttribute($entrada);
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
        $alumnos = Alumno::select('nia')->QGrupo($grupo->codigo)->get()->toArray();
        return $query->whereIn('idAlumno', $alumnos);
    }

    public function getQuienAttribute()
    {
        return $this->fullName;
    }

    public function getSaoAnnexesAttribute()
    {
        return Adjunto::where('size', 1024)->where('route', 'alumnofctaval/'.$this->id)->count();
    }

    public function getA2Attribute()
    {
        return Signatura::where('idSao', $this->idSao)->where('tipus', 'A2')->where('signed', true)->get()->first();
    }

    public function getA1Attribute()
    {
        return Signatura::where('idSao', $this->idSao)->where('tipus', 'A1')->where('signed', true)->get()->first();
    }

    public function getA3Attribute()
    {
        return Signatura::where('idSao', $this->idSao)->where('tipus', 'A3')->get()->first();
    }

    public function routeFile($anexe)
    {
        return storage_path('app/annexes/')."A{$anexe}_{$this->idSao}.pdf";
    }

    public function getIdPrintAttribute()
    {
        return $this->idFct.'-'.$this->nombre;
    }

    public function getClassAttribute()
    {
        return ($this->asociacion === 3) ? 'bg-purple':
            ((fechaInglesa($this->hasta) <= Hoy('Y-m-d')) ?'bg-blue-sky':'');
    }
}
