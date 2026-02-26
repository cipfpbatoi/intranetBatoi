<?php

namespace Intranet\Entities;

use Intranet\Application\Grupo\GrupoService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Intranet\Events\ActivityReport;
use \DB;
use Intranet\Notifications\MyResetPassword;


/**
 * Model de professor.
 */
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
    protected $hidden = ['password', 'api_token', 'remember_token'];
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
        'departamento',
        'idioma',
        'movil1',
        'movil2',
        'mostrar',
        'especialitat',
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
        'email' => ['type' => 'email'],
        'departamento' => ['type' => 'select'],
        'idioma' => ['type' => 'select'],
        'mostrar' => ['type' => 'checkbox'],
    ];
    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    use Notifiable;
    use \Intranet\Entities\Concerns\BatoiModels;
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

    public function Cotxes(): HasMany
    {
        return $this->hasMany(Cotxe::class, 'idProfesor', 'dni');
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
        return $query
            ->where('activo', 1)
            ->where(function ($q) {
                $q->whereNull('fecha_baja')
                    ->orWhere('fecha_baja', '')
                    ->orWhere('fecha_baja', '0000-00-00');
            });
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
        $grupos = app(GrupoService::class)->byCurso(2)
            ->pluck('tutor')
            ->filter()
            ->unique()
            ->values()
            ->all();
        return $query->Plantilla()->whereIn('dni', $grupos);
    }

    public function scopeGrupo($query, $grupo)
    {
        $profesores = Horario::Grup($grupo)->distinct()->pluck('idProfesor')->toArray();

        return $query->where(function ($q) use ($profesores) {
            $q->whereIn('dni', $profesores)
                ->orWhereIn('sustituye_a', $profesores);
        })->Plantilla();
    }
    public function scopeGrupoT($query, $grupoT)
    {
        $profesores = Miembro::query()
            ->where('idGrupoTrabajo', '=', $grupoT)
            ->pluck('idProfesor')
            ->filter()
            ->values()
            ->all();
        return $query->whereIn('dni', $profesores)->Activo();
    }

    public function scopeApiToken($query, $api)
    {
        return $query->where('api_token', $api);
    }

    public function getfechaIngresoAttribute($fecha)
    {
        if ($fecha) {
            $fecha = new Carbon($fecha);
            return $fecha->format('d-m-Y');
        }
    }

    public function getFechaNacAttribute($fecha)
    {
        if ($fecha) {
            $fecha = new Carbon($fecha);
            return $fecha->format('d-m-Y');
        }
    }

    public function getFechaBajaAttribute($fecha)
    {
        if ($fecha) {
            $fecha = new Carbon($fecha);
            return $fecha->format('d-m-Y');
        }
    }
    
    public function getIdiomaOptions()
    {
        return config('auxiliares.idiomas');
    }

    public function getIdAttribute()
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
        return optional(Falta_profesor::Hoy($this->dni)->get()->last())->entrada ?? ' ';
    }
    public function getSalidaAttribute()
    {
        return optional(Falta_profesor::Hoy($this->dni)->get()->last())->salida ?? ' ';
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

    public function getSurNamesAttribute()
    {
        return ucwords(mb_strtolower($this->apellido1 . ' ' . $this->apellido2, 'UTF-8'));
    }

    public function getShortNameAttribute()
    {
       return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1, 'UTF-8'));
    }

    public function getAhoraAttribute()
    {
        $sesion = (int) (sesion(Hora(now())) ?? 0);
        $dia = (string) (nameDay(hoy()) ?? '');
        if ($sesion <= 0 || $dia === '') {
            return '';
        }

        $horaActual = $this->Horari->where('dia_semana', $dia)->where('sesion_orden', $sesion)->first();
        if ($horaActual) {
            if ($horaActual->ocupacion != null && isset($horaActual->Ocupacion?->nombre)) {
                return $horaActual->Ocupacion->nombre;
            }
            if ($horaActual->modulo != null && isset($horaActual->Modulo?->cliteral) && $horaActual->Grupo?->nombre) {
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
        $miGrupo = app(GrupoService::class)->byTutorOrSubstitute((string) $this->dni, (string) ($this->sustituye_a ?? ''));
        if (isset($miGrupo?->codigo)) {
            return $miGrupo->codigo;
        }
        $miGrupoDual = app(GrupoService::class)->firstByTutorDual((string) $this->dni);
        return $miGrupoDual->codigo ?? '';
     }

    public function getFileNameAttribute()
    {
        $foto = (string) ($this->foto ?? '');
        $pos1 = strpos($foto, '.');
        if ($foto === '' || $pos1 === false || $pos1 <= 6) {
            return '';
        }
        return substr($foto, 6, $pos1 - 6);

    }

    public function getSubstitutAttribute()
    {
        $substitut = $this->Sustituye??null;
        return isset($substitut->fullName)?$substitut->fullName:'';
    }

    public function getSustituidosAttribute()
    {
        return self::getSubstituts((string) $this->dni);
    }


    public static function getSubstituts($dni)
    {
        $actualDni = (string) $dni;
        $sustituidos = [];
        $visited = [];

        while ($actualDni !== '' && !isset($visited[$actualDni])) {
            $visited[$actualDni] = true;
            $sustituidos[] = $actualDni;

            $profesor = Profesor::find($actualDni);
            if (!$profesor) {
                break;
            }

            $next = trim((string) ($profesor->sustituye_a ?? ''));
            if ($next === '') {
                break;
            }
            $actualDni = $next;
        }

        return $sustituidos;
    }

    public function getHasCertificateAttribute()
    {
        return file_exists($this->pathCertificate);
    }

    public function getPathCertificateAttribute()
    {
        $fileName = $this->getFileNameAttribute();
        return storage_path('app/zip/'.$fileName.'.tmp');
    }

}
