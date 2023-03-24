<?php

namespace Intranet\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use \DB;
use Intranet\Events\PreventAction;
use Intranet\Notifications\MyResetPassword;


class Profesor extends Authenticatable
{
    /*     * ************************************************************************
     * $keyType -> solo si no es entero. Da errores si no se pone
     * $visible -> Para mostrar en el método show
     * $fillable -> Aquellos que son actualizables en masa. Se utiliza para construir
     *              los edit y create
     * $rules   -> Para comprobar los devuelto. Se utiliza para marcar los requeridos
     * $inputTypes -> Tipo de input para los formularios de crear y editar.
     *                Si no se indica nada se muestran como tetx
     * Notifiable -> Para que se le puedan mandar mensajes
     * BatoiModels -> Imprescindible si se trata con campos de fecha
     * getcampoAttribute -> Para que devuelva la fecha en un formato dado
     *
     */

    public $primaryKey = 'dni';
    protected $table = 'profesores';
    protected $keyType = 'string';
    protected $hidden = ['password', 'api_token', 'rembember_token'];
    protected $visible = [
        'dni',
        'codigo',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'emailItaca',
        'domicilio',
        'movil1',
        'movil2',
        'sexo',
        'codigo_postal',
        'departamento',
        'fecha_ingreso',
        'fecha_baja',
        'fecha_nac',
        'foto',
        'rol',
        'idioma',
        'mostrar'
    ];
    protected $fillable = [
        'codigo',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'foto',
        'departamento',
        'idioma',
        'mostrar',
        'especialitat',
        'signatura',
        'peu',
    ];
    protected $casts = [
        'codigo' => 'integer',
        'email' => 'string',
        'foto' => 'string',
    ];
    protected $rules = [
        'email' => 'required|email',
        'foto' => 'image',
    ];
    protected $inputTypes = [
        'codigo' => ['type' => 'hidden'],
        'nombre' => ['disabled' => 'disabled'],
        'apellido1' => ['disabled' => 'disabled'],
        'apellido2' => ['disabled' => 'disabled'],
        'foto' => ['type' => 'file'],
        'email' => ['type' => 'email'],
        'departamento' => ['type' => 'select'],
        'idioma' => ['type' => 'select'],
        'mostrar' => ['type' => 'checkbox'],
        'signatura' => ['type' => 'file'],
        'peu' => ['type' => 'file'],
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    use Notifiable;
    use BatoiModels;
    use \Illuminate\Auth\Passwords\CanResetPassword;

    public function Comision()
    {
        return $this->hasMany(Comision::class, 'idProfesor', 'dni');
    }

    public function Faltas()
    {
        return $this->hasMany(Falta_profesor::class, 'idProfesor', 'dni');
    }

    public function Actividad()
    {
        return $this->belongsToMany(Actividad::class, 'actividad_profesor', 'idActividad', 'idProfesor');
    }

    public function Departamento()
    {
        return $this->hasOne(Departamento::class, 'id', 'departamento');
    }

    public function Sustituye()
    {
        return $this->hasOne(Profesor::class, 'sustituye_a', 'dni');
    }

    public function Reserva()
    {
        return $this->hasMany(Reserva::getClass(), 'profesor_id', 'dni');
    }

    public function Horari()
    {
        return $this->hasMany(Horario::class, 'idProfesor', 'dni');
    }


    public function grupos()
    {
        $grupos = ['COCOPE', 'Claustro', str_replace(' ', '_', $this->Departamento->cliteral)];
        $gts = GrupoTrabajo::MisgruposTrabajo()->get();
        foreach ($gts as $gt) {
            $grupos[] = str_replace(' ', '_', $gt->literal);
        }
        return $grupos;
    }

    public function Activity()
    {
        return $this->hasMany(Activity::class, 'author_id');
    }

    public function scopeActivo($query)
    {
        return $query->where('fecha_baja', null)->where('activo', 1);
    }

    public static function getRol($rol)
    {
        $all = Profesor::Activo()->get();
        $data = [];
        foreach ($all as $profesor) {
            if ($profesor->rol % $rol == 0) {
                $data[$profesor->dni]=$profesor->fullName;
            }
        }
        return $data;
    }

    public function scopePlantilla($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeTutoresFCT($query)
    {
        $grupos = hazArray(Grupo::where('curso', 2)->get(), 'tutor', 'tutor');
        return $query->Plantilla()->whereIn('dni', $grupos);
    }

    public function scopeGrupo($query, $grupo)
    {
        $profesores = Horario::distinct()->select('idProfesor')->Grup($grupo)->get()->toArray();
        return $query->whereIn('dni', $profesores)->orWhereIn('sustituye_a', $profesores)->Activo();
    }
    public function scopeGrupoT($query, $grupoT)
    {
        $profesores = Miembro::distinct()
            ->select('idProfesor')
            ->where('idGrupoTrabajo', '=', $grupoT)
            ->get()
            ->toArray();
        return $query->whereIn('dni', $profesores)->Activo();
    }

    public function scopeApiToken($query, $api)
    {
        return $query->where('api_token', $api);
    }

    public function getfechaIngresoAttribute($fecha)
    {
        if ($fecha) {
            $fecha = new Date($fecha);
            return $fecha->format('d-m-Y');
        }
    }

    public function getFechaNacAttribute($fecha)
    {
        if ($fecha) {
            $fecha = new Date($fecha);
            return $fecha->format('d-m-Y');
        }
    }

    public function getFechaBajaAttribute($fecha)
    {
        if ($fecha) {
            $fecha = new Date($fecha);
            return $fecha->format('d-m-Y');
        }
    }
    
    public function getIdiomaOptions()
    {
        return config('auxiliares.idiomas');
    }

    public function getIdAttribute($cifrar)
    {
        return $this->dni;
    }

    public function getDepartamentoOptions()
    {
        return hazArray(Departamento::All(), 'id', 'vliteral');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }

    public function getXrolAttribute()
    {
        return implode(',', nameRolesUser($this->rol));
    }

    public function getXdepartamentoAttribute()
    {
        return $this->Departamento->depcurt??$this->departamento;
    }
    public function getLdepartamentoAttribute()
    {
        return $this->Departamento->cliteral??'No assignat';
    }

    public function getEntradaAttribute()
    {
        return Falta_profesor::Hoy($this->dni)->get()->last()->entrada??' ';
    }
    public function getSalidaAttribute()
    {
        return Falta_profesor::Hoy($this->dni)->get()->last()->salida??' ';
    }
    public function getHorarioAttribute()
    {
        $horario = Horario::Primera($this->dni)->orderBy('sesion_orden')->first();
        return (isset($horario->desde))?$horario->desde." - ".$horario->hasta:'';
    }
    
    public function getFullNameAttribute()
    {
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2, 'UTF-8'));
    }
    public function getNameFullAttribute()
    {
        return ucwords(mb_strtolower($this->apellido1 . ' ' . $this->apellido2.', '.$this->nombre, 'UTF-8'));
    }

    public function getShortNameAttribute()
    {
       return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1, 'UTF-8'));
    }

    public function getAhoraAttribute()
    {
        $sesion = sesion(Hora(now()));
        $dia = config("auxiliares.diaSemana." . now()->format('w'));
        $horaActual = $this->Horari->where('dia_semana', $dia)->where('sesion_orden', $sesion)->first();
        if ($horaActual) {
            if ($horaActual->ocupacion != null && isset($horaActual->Ocupacion->nombre)) {
                return $horaActual->Ocupacion->nombre;
            }
            if ($horaActual->modulo != null && isset($horaActual->Modulo->cliteral) && $horaActual->Grupo->nombre) {
                return $horaActual->Modulo->literal . ' (' . $horaActual->aula . ')';
            }
        }
        return '';

    }



    public function getMiJefeAttribute()
    {
        $todos = Profesor::where('departamento', $this->departamento)->where('activo', 1)->get();
        foreach ($todos as $uno) {
            if (esRol($uno->rol, config('roles.rol.jefe_dpto'))) {
                return $uno->dni;
            }
        }
    }

    public function getQualitatFile()
    {
        $find = Documento::where('idProfesor', $this->dni)->where('tipoDocumento', 'Qualitat')
                ->where('curso', curso())->first();
        if ($find) {
            return $find->fichero;
        } else {
            return false;
        }
    }

    public function getGrupoTutoriaAttribute()
    {
        $miGrupo = Grupo::where('tutor', '=', authUser()->dni)->get();
        if (isset($miGrupo->first()->codigo)) {
            return $miGrupo->first()->codigo;
        } else {
            $miGrupo = Grupo::where('tutorDual', '=', authUser()->dni)->get();
            return isset($miGrupo->first()->codigo) ? $miGrupo->first()->codigo : '';
        }
    }

}
