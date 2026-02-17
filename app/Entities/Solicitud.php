<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;
use Jenssegers\Date\Date;

class Solicitud extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    public $timestamps = false;
    public $table = 'solicitudes';


    protected $fillable = [
        'idAlumno',
        'idProfesor',
        'text1','text2','text3',
        'idOrientador',
        'fecha',
    ];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idProfesor' => ['type' => 'hidden'],
        'text1' => ['type' => 'textarea'],
        'text2' => ['type' => 'textarea'],
        'text3' => ['type' => 'textarea'],
        'idOrientador' => ['type' => 'select'],
        'fecha' => ['type' => 'date'],
    ];
    
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

    public function getIdOrientadorOptions()
    {
        $orientador = [];
        foreach (usersWithRol(config('roles.rol.orientador')) as $dni) {
            $orientador[$dni] = Profesor::find($dni)->fullName;
        }
        return $orientador;
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
    public function Orientador()
    {
        return $this->belongsTo(Profesor::class, 'idOrientador', 'dni');
    }

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }

    public function getNomAlumAttribute()
    {
        return $this->Alumno->FullName;
    }
    public function getSituacionAttribute()
    {
        return isblankTrans('models.Solicitud.'.$this->estado)
            ? trans('messages.situations.'.$this->estado)
            : trans('models.Solicitud.' . $this->estado);
    }

    public function getMotiuAttribute()
    {
        return substr($this->text1, 0, 75);
}
    public function getQuienAttribute()
    {
        return $this->nomAlumn;
    }
    public function scopeListos($query)
    {
        return $query->where('estado', 2);
    }

}
