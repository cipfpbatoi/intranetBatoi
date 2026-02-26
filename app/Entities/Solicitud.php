<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Presentation\Crud\SolicitudCrudSchema;
use Illuminate\Support\Carbon;

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
    protected $inputTypes = SolicitudCrudSchema::INPUT_TYPES;
    
    public function getfechaAttribute($entrada)
    {
        if (empty($entrada)) {
            return '';
        }
        $fecha = new Carbon($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getfechasolucionAttribute($salida)
    {
        if (empty($salida)) {
            return '';
        }
        $fecha = new Carbon($salida);
        return $fecha->format('d-m-Y');
    }

    public function getIdOrientadorOptions()
    {
        $orientadorDnis = usersWithRol(config('roles.rol.orientador'));
        if ($orientadorDnis === []) {
            return [];
        }

        return Profesor::query()
            ->whereIn('dni', $orientadorDnis)
            ->get()
            ->mapWithKeys(fn (Profesor $profesor) => [$profesor->dni => $profesor->fullName])
            ->all();
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
        return $this->Alumno->FullName ?? '';
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
        return $this->nomAlum;
    }
    public function scopeListos($query)
    {
        return $query->where('estado', 2);
    }

}
