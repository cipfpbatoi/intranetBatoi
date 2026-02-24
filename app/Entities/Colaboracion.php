<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\ColaboracionCrudSchema;


class Colaboracion extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'colaboraciones';
    protected $fillable = [
        'idCentro',
        'idCiclo',
        'contacto',
        'telefono',
        'email',
        'puestos',
        'tutor'];
    protected $rules = ColaboracionCrudSchema::RULES;
    protected $inputTypes = ColaboracionCrudSchema::INPUT_TYPES;

    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    protected $attributes = [
        'puestos' => 1,
        'estado' => 1
     ];


    public function Centro()
    {
        return $this->belongsTo(Centro::class, 'idCentro', 'id');
    }

    public function Ciclo()
    {
        return $this->belongsTo(Ciclo::class, 'idCiclo', 'id');
    }
    public function fcts()
    {
        return $this->hasMany(Fct::class, 'idColaboracion', 'id');
    }
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class, 'idColaboracion', 'id');
    }
    public function Propietario()
    {
        return $this->belongsTo(Profesor::class, 'tutor', 'dni');
    }
    public function votes()
    {
        return $this->hasMany(VoteAnt::class, 'idColaboracion', 'id');
    }

    public function scopeCiclo($query, $ciclo)
    {
        return $query->where('idCiclo', $ciclo);
    }
    public function scopeEmpresa($query, $empresa)
    {
        $centros = Centro::query()
            ->Empresa($empresa)
            ->pluck('id')
            ->all();

        return $query->whereIn('idCentro', $centros);
    }
    public function scopeMiColaboracion($query, $empresa=null, $dni=null)
    {
        $dni = $dni??authUser()->dni;
        $ciclo = app(GrupoService::class)->qTutor((string) $dni)
            ->pluck('idCiclo')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($empresa) {
            return $query->whereIn('idCiclo', $ciclo)->Empresa($empresa);
        }
        return $query->whereIn('idCiclo', $ciclo);
    }

    public function getEmpresaAttribute()
    {
        return $this->Centro->nombre ?? '';
    }
    public function getShortAttribute()
    {
        return substr((string) ($this->Centro->nombre ?? ''), 0, 50);
    }
    public function getXCicloAttribute()
    {
        return $this->Ciclo->ciclo ?? '';
    }

    public function getXEstadoAttribute()
    {
        return config('auxiliares.estadoColaboracion')[$this->estado] ?? '';
    }
    public function getLocalidadAttribute()
    {
        $localidad = $this->Centro->localidad ?? null;

        return $localidad ? strtoupper((string) $localidad) : 'Desconeguda';
    }
    public function getHorariAttribute()
    {
        return $this->Centro->horarios ?? '';
    }

    public function getEstadoOptions()
    {
        return config('auxiliares.estadoColaboracion');
    }

    public function getAnotacioAttribute()
    {
        return Activity::modelo('Colaboracion')
            ->id($this->id)
            ->where('action', 'book')
            ->orderBy('created_at')
            ->pluck('comentari')
            ->implode("\n");
    }
    public function getProfesorAttribute()
    {
        return $this->Propietario->fullName??'';
    }
    public function getUltimoAttribute()
    {
        return $this->updated_at;
    }

    public function getSituationAttribute()
    {
        if ($this->tutor == '' && $this->estado == 1) {
            return 1;
        }
        if ($this->estado == 1 || $this->estado == 3) {
            return 2;
        }
        if ($this->estado == 2) {
            return 3;
        }
        return 1;
    }


}
