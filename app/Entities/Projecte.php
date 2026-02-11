<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Intranet\Events\ActivityReport;
use Jenssegers\Date\Date;

class Projecte extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $fillable = [
        'idAlumne',
        'grup',
        'estat',
        'titol',
        'objectius',
        'resultats',
        'aplicacions',
        'recursos',
        'descripcio',
        'observacions',
        'defensa',
        'hora_defensa'
    ];
    protected $inputTypes = [
        'defensa' => ['type' => 'date'],
        'hora_defensa' => ['type' => 'time']
    ];


    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumne', 'nia');
    }

    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'grup', 'codigo');
    }

    public function getStatusAttribute()
    {
         return config('auxiliares.estadoProjecte')[$this->estat];
    }

    public function getAlumneAttribute()
    {
        return $this->Alumno->fullName;
    }

    public function getGrupOptions()
    {
         return hazArray(AuthUser()->Grupo, 'codigo', 'nombre');
    }

    public function getIdAlumneOptions()
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)->orWhere('tutor', '=', authUser()->sustituye_a)->first();
        return hazArray($miGrupo->Alumnos,'nia','fullName');

    }

    public function getDefensaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

}
