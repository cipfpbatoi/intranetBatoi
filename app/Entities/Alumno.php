<?php

namespace Intranet\Entities;

use Intranet\Application\Grupo\GrupoService;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class Alumno extends Authenticatable
{
    use Notifiable, \Intranet\Entities\Concerns\BatoiModels;

    public $primaryKey = 'nia';
    public $keyType = 'string';
    protected static $tabla = 'alumnos';

    protected $visible = [
        'nia', 'dni', 'nombre', 'apellido1', 'apellido2', 'email',
        'expediente', 'domicilio', 'municipio', 'provincia',
        'telef1', 'telef2', 'sexo', 'codigo_postal',
        'departamento', 'fecha_ingreso', 'fecha_matricula',
        'fecha_nac', 'foto', 'turno', 'trabaja', 'repite','DA'
    ];

    protected  $fillable = [
        'codigo', 'nombre', 'apellido1', 'apellido2', 'email',
        'telef1', 'telef2', 'foto', 'idioma',
        'imageRightAccept', 'outOfSchoolActivityAccept', 'DA'
    ];

    protected array $rules = [
        'email' => 'required|email',
    ];

    protected array $inputTypes = [
        'codigo' => ['type' => 'hidden'],
        'nombre' => ['disabled' => 'disabled'],
        'apellido1' => ['disabled' => 'disabled'],
        'apellido2' => ['disabled' => 'disabled'],
        'foto' => ['type' => 'file'],
        'email' => ['type' => 'email'],
        'idioma' => ['type' => 'select'],
        'imageRightAccept' => ['disabled' => 'disabled'],
        'outOfSchoolActivityAccept' => ['disabled' => 'disabled'],
        'DA' => ['type'=>'hidden']
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONS
    |--------------------------------------------------------------------------
    */
    public function Curso(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'alumnos_cursos', 'idAlumno', 'idCurso', 'nia', 'id')
            ->withPivot(['registrado', 'finalizado']);
    }

    public function Colaboracion(): BelongsToMany
    {
        return $this->belongsToMany(Colaboracion::class, 'colaboraciones', 'idAlumno', 'idColaboracion');
    }

    public function Grupo(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'alumnos_grupos', 'idAlumno', 'idGrupo');
    }

    public function Fcts(): BelongsToMany
    {
        return $this->belongsToMany(Fct::class, 'alumno_fcts', 'idAlumno', 'idFct', 'nia', 'id')
            ->withPivot(['calificacion', 'calProyecto', 'actas', 'insercion']);
    }

    public function AlumnoFct(): HasMany
    {
        return $this->hasMany(AlumnoFct::class, 'idAlumno', 'nia');
    }

    public function FctsColaboracion(int $colaboracion): BelongsToMany
    {
        return $this->Fcts()->wherePivot('idColaboracion', $colaboracion);
    }

    public function AlumnoResultado(): HasMany
    {
        return $this->hasMany(AlumnoResultado::class, 'idAlumno', 'nia');
    }

    public function Provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'provincia', 'id');
    }

    public function Municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio', 'cod_municipio')
            ->where('provincias_id', $this->provincia);
    }

    public function Projecte()
    {
        return $this->hasOne(Projecte::class, 'idAlumne', 'nia');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeQGrupo(Builder $query, string|array $grupo): Builder
    {
        $alumnos = AlumnoGrupo::whereIn('idGrupo', (array) $grupo)->pluck('idAlumno');
        return $query->whereIn('nia', $alumnos);
    }

    public function scopeMenor(Builder $query, ?string $fecha = null): Builder
    {
        $fechaLimite = ($fecha ? new Carbon($fecha) : new Carbon())->subYears(18)->toDateString();
        return $query->where('fecha_nac', '>', $fechaLimite);
    }

    public function scopeMisAlumnos(Builder $query, ?string $profesor = null, bool $dual = false): Builder
    {
        $profesor = $profesor ?? authUser()->dni;
        $grupos = app(GrupoService::class)->qTutor((string) $profesor)->pluck('codigo')->toArray();
        $alumnos = AlumnoGrupo::whereIn('idGrupo', $grupos)->pluck('idAlumno');
        return $query->whereIn('nia', $alumnos);
    }


    public function scopeMisLOE(Builder $query, ?string $profesor = null, bool $dual = false): Builder
    {
        $profesor = $profesor ?? authUser()->dni;

        // Subconsulta dels codis de grup on sóc tutor i NO són LFP
        $codigosTutorNoLfp = app(GrupoService::class)
            ->qTutor((string) $profesor)
            ->where('codigo', 'not like', '%LFP%')
            ->pluck('codigo')
            ->all();

        // idAlumno de la taula pont per a eixos grups
        $subAlumnos = AlumnoGrupo::query()
            ->select('idAlumno')
            ->whereIn('idGrupo', $codigosTutorNoLfp);

        return $query->whereIn('nia', $subAlumnos);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */
    public function getIdGrupoAttribute(): ?string
    {
        return optional($this->Grupo->first())->codigo;
    }

    public function getIdAttribute(): ?string
    {
        return $this->nia;
    }

    public function getHorasFctAttribute(): int
    {
        return $this->AlumnoFct->sum('horas');
    }

    public function getDepartamentoAttribute(): string
    {
        return optional($this->Grupo->first()?->Ciclo)->departamento ?? '99';
    }

    public function getTutorAttribute()
    {
        if ($this->Grupo->isEmpty()) {
            return collect(); // Retorna una col·lecció buida
        }

        return $this->Grupo->map(fn($grupo) => $grupo->Tutor)->flatten()->unique();
    }

    public function getFechaNacAttribute(?string $entrada): ?string
    {
        return $entrada ? (new Carbon($entrada))->format('d-m-Y') : null;
    }

    public function getPoblacionAttribute(): string
    {
        return optional($this->Municipio)->municipio ?? 'NO TROBAT';
    }

    public function getEsMenorAttribute(): bool
    {
        return $this->fecha_nac ? (new Carbon($this->fecha_nac))->gt((new Carbon())->subYears(18)) : false;
    }

    public function esMenorEdat($fecha)
    {
        $dataNaixement = new \Illuminate\Support\Carbon($this->fecha_nac);
        $dataComparacio = new \Illuminate\Support\Carbon($fecha);

        return $dataNaixement->diffInYears($dataComparacio) < 18;
    }

    public function getEdatAttribute(): ?int
    {
        return $this->fecha_nac ? (new Carbon($this->fecha_nac))->age : null;
    }

    public function getFullNameAttribute(): string
    {
        return ucwords(mb_strtolower(trim("{$this->nombre} {$this->apellido1} {$this->apellido2}"), 'UTF-8'));
    }

    public function getShortNameAttribute(): string
    {
        return ucwords(mb_strtolower(trim("{$this->nombre} {$this->apellido1}"), 'UTF-8'));
    }

    public function getNameFullAttribute(): string
    {
        return ucwords(mb_strtolower(trim("{$this->apellido1} {$this->apellido2}, {$this->nombre}"), 'UTF-8'));
    }

    public function getDualNameAttribute(): string
    {
        return ucfirst(mb_strtolower($this->apellido1 ?? '')) . ucfirst(mb_strtolower($this->nombre ?? ''));
    }


    /*
    |--------------------------------------------------------------------------
    | MÈTODES UTILS
    |--------------------------------------------------------------------------
    */
    public function getIdiomaOptions(): array
    {
        return config('auxiliares.idiomas');
    }

    public function scopeMisDA(Builder $query, ?string $profesor = null, bool $dual = false): Builder
    {
        $profesor = $profesor ?? authUser()->dni;
        $grupos = app(GrupoService::class)->qTutor((string) $profesor)->pluck('codigo')->toArray();
        $alumnos = AlumnoGrupo::whereIn('idGrupo', $grupos)->pluck('idAlumno');
        return $query->whereIn('nia', $alumnos)->where('DA',1);
    }
}
