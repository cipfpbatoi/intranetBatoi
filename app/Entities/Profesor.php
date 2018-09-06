<?php

namespace Intranet\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jenssegers\Date\Date;
use Intranet\Notifications\mensajePanel;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Activity;
use Intranet\Events\ActivityReport;
use \DB;
use Intranet\Events\PreventAction;
use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Departamento;
use Intranet\Notifications\MyResetPassword;
use Illuminate\Support\Facades\App;

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
        return $this->hasMany(Comision::getClass(), 'idProfesor', 'dni');
    }

    public function Faltas()
    {
        return $this->hasMany(Faltas::getClass(), 'idProfesor', 'dni');
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

    public function Horario()
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

    public function scopePlantilla($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeGrupo($query, $grupo)
    {
        $profesores = Horario::distinct()->select('idProfesor')->Grup($grupo)->get()->toArray();
        return $query->whereIn('dni', $profesores)->orWhereIn('sustituye_a', $profesores)->Activo();
    }
    public function scopeGrupoT($query, $grupoT)
    {
        $profesores = Miembro::distinct()->select('idProfesor')->where('idGrupoTrabajo', '=', $grupoT)->get()->toArray();
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
    
    

//    public function nombre()
//    {
//        return $this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2;
//    }
//
//    public function nombreCorto()
//    {
//        return $this->nombre . ' ' . $this->apellido1;
//    }

    public static function Baja($id, $fecha = null)
    {
        $profe = Profesor::find($id);
        if ($fecha){
            $profe->fecha_baja = new Date($fecha);
            $profe->save();
        }
        else {
            DB::transaction(function() use ($profe) {
                $profe->fecha_baja = null;
                $profe->save();
                if ($sustituto = $profe->Sustituye) {
                        //canvi d'horari
                    if (Horario::profesor($profe->dni)->count()==0)
                        Horario::where('idProfesor',$sustituto->dni)->update(['idProfesor'=> $profe->dni]);
                    else
                        Horario::where('idProfesor',$sustituto->dni)->delete();
                               //reuniones
                    foreach (Asistencia::where('idProfesor',$sustituto->dni)->get() as $asistencia){
                        if (Asistencia::where('idProfesor', $profe->dni)->where('idReunion', $asistencia->idReunion)->count() == 0){
                            Reunion::find($asistencia->idReunion)->profesores()->syncWithoutDetaching([$profe->dni=>['asiste'=>0]]);
                        }
                    }
                    
                    Reunion::where('idProfesor',$sustituto->dni)->update(['idProfesor'=>$profe->dni]);
                    Grupo::where('tutor',$sustituto->dni)->update(['tutor'=>$profe->dni]);
                    Programacion::where('idProfesor',$sustituto->dni)->update(['idProfesor' => $profe->dni]);
                    Expediente::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profe->dni]);
                    Resultado::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profe->dni]);
                    
                    $sustituto->sustituye_a = ' ';
                    $sustituto->activo = 0;
                    $sustituto->save();
                }
            });
        }
    }

    public function getIdiomaOptions()
    {
        return config('auxiliares.idiomas');
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
        return implode(',', NameRolesUser($this->rol));
    }

    public function getXdepartamentoAttribute()
    {
        return isset($this->Departamento->depcurt)?$this->Departamento->depcurt:$this->departamento;
    }
    public function getLdepartamentoAttribute()
    {
        return isset($this->Departamento->cliteral)?$this->Departamento->literal:'No assignat';
    }

    public function getEntradaAttribute()
    {
        return isset(Falta_profesor::Hoy($this->dni)->last()->entrada)?Falta_profesor::Hoy($this->dni)->last()->entrada:' ';
    }
    public function getSalidaAttribute()
    {
        return isset(Falta_profesor::Hoy($this->dni)->last()->salida)?Falta_profesor::Hoy($this->dni)->last()->salida:' ';
    }
    public function getHorarioAttribute()
    {
        $horario = Horario::Primera($this->dni)->orderBy('sesion_orden')->first();
        return (isset($horario->desde))?$horario->desde." - ".$horario->hasta:'';
    }
    
    public function getFullNameAttribute()
    {
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2,'UTF-8'));
    }
    public function getNameFullAttribute()
    {
        return ucwords(mb_strtolower($this->apellido1 . ' ' . $this->apellido2.', '.$this->nombre ,'UTF-8'));
    }

    public function getShortNameAttribute()
    {
       return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1 ,'UTF-8'));
    }

    public function getAhoraAttribute()
    {
        return $this->donde_esta($this->dni)['ahora'];
    }

    public function getMomentoAttribute()
    {
        return $this->donde_esta($this->dni)['momento'];
    }

    public function getMiJefeAttribute()
    {
        $todos = Profesor::where('departamento', $this->departamento)->get();
        foreach ($todos as $uno)
            if (esRol($uno->rol, config('roles.rol.jefe_dpto')))
                return $uno->dni;
    }
    public function getQualitatFile(){
        $find = Documento::where('idProfesor', $this->dni)->where('tipoDocumento','Qualitat')
                ->where('curso',Curso())->first();
        if ($find) return $find->fichero;
        else return false;
    }

    /**
     * Devuelve salida para preparar el boton de donde esta el profesor actualmente
     *
     * @param  string dni profesor
     * @return array
     */
    protected function donde_esta($profesor)
    {
        $ahora = Date::now();
        $hora = sesion(Hora($ahora));
        $dia = config("auxiliares.diaSemana." . $ahora->format('w'));

        $horasDentro = Horario::Dia($dia)
                ->Profesor($profesor)
                ->orderBy('sesion_orden')
                ->get();
        //dd($horasDentro);
        if (count($horasDentro) > 0) {
            if ($horasDentro->last()->sesion_orden < $hora)
                return ['momento' => $horasDentro->last()->hasta, 'ahora' => trans('messages.generic.home')];
            if ($horasDentro->first()->sesion_orden > $hora)
                return ['momento' => $horasDentro->first()->desde, 'ahora' => trans('messages.generic.home')];

            $horaActual = Horario::Profesor($profesor)
                    ->Dia($dia)
                    ->Orden($hora)
                    ->orderBy('sesion_orden')
                    ->first();
            if ($horaActual) {
                if ($horaActual->modulo != null && isset($horaActual->Modulo->cliteral) && $horaActual->Grupo->nombre) 
                    return ['momento' => $horaActual->Grupo->nombre, 'ahora' => $horaActual->Modulo->literal . ' (' . $horaActual->aula . ')'];
                if ($horaActual->ocupacion != null && isset($horaActual->Ocupacion->nombre)) {
                    if (App::getLocale(session('lang')) == 'es')
                        return ['momento' => '', 'ahora' => $horaActual->Ocupacion->nombre];
                    else
                        return ['momento' => '', 'ahora' => $horaActual->Ocupacion->nom];
                }
            } else
                return ['momento' => trans('messages.generic.patio'), 'ahora' => trans('messages.generic.patio')];
        } else
            return ['momento' => trans('messages.generic.notoday'), 'ahora' => trans('messages.generic.home')];
    }

}
