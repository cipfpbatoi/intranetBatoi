<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Presentation\Crud\TaskCrudSchema;
use Illuminate\Support\Carbon;
use Intranet\Services\School\TaskFileService;


class Task extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;
    protected $fillable = [
        'descripcion',
        'vencimiento',
        'destinatario',
        'informativa',
        'fichero',
        'enlace',
        'action',
        'activa'
    ];
    protected $inputTypes = TaskCrudSchema::INPUT_TYPES;


    public function Profesores()
    {
        return $this->belongsToMany(
            Profesor::class,
            'tasks_profesores',
            'id_task',
            'id_profesor',
            'id',
            'dni'
        )->withPivot('check', 'valid')->withTimestamps();
    }

    public function scopeMisTareas($query, $profesor=null)
    {
        $profesor = Profesor::find($profesor) ?? authUser();
        if (!$profesor) {
            return $query->whereRaw('1 = 0');
        }
        $rolesProfesor = rolesUser($profesor->rol);
        return $query->whereIn('destinatario', $rolesProfesor)
            ->where('activa', 1);
    }

    public function getmyDetailsAttribute()
    {
        $teacher = authUser()->dni ?? null;
        if (!$teacher) {
            return null;
        }

        return $this->profesores()->where('dni', $teacher)->first();
    }

    public function getValidAttribute()
    {
        $taskTeacher = $this->myDetails;
        if (!$taskTeacher) {
            return 0;
        } elseif ($taskTeacher->pivot->valid) {
            return  1;
        } else {
            return $this->informativa;
        }
    }

    public function getLinkAttribute()
    {
        return $this->fichero?'/storage/'.$this->fichero:$this->enlace;
    }

    public function getVencimientoAttribute($entrada)
    {
        if (empty($entrada)) {
            return '';
        }
        $fecha = new Carbon($entrada);
        return $fecha->format('d-m-Y');
    }



    public function getImageAttribute()
    {
        $vencimiento = $this->getRawOriginal('vencimiento') ?: ($this->attributes['vencimiento'] ?? null);
        if (empty($vencimiento)) {
            return $this->informativa ? 'informacion.jpeg' : 'task.png';
        }

        $vencimientoDate = (new Carbon($vencimiento))->format('Y-m-d');
        if ($vencimientoDate <= date('Y-m-d')) {
            return 'warning.png';
        }

        return $this->informativa ? 'informacion.jpeg' : 'task.png';
    }

    public function getDestinoAttribute()
    {
        $roles = config('roles.lor');
        return $roles[$this->destinatario] ?? ('Desconegut (' . $this->destinatario . ')');
    }

    public function getDestinatarioOptions()
    {
        return config('roles.lor');
    }

    public function getActionOptions()
    {
        return config('roles.actions');
    }

    public function getAccioAttribute()
    {
        return $this->action ? (config('roles.actions')[$this->action] ?? '') : '';
    }

    public function fillFile($file)
    {
        return app(TaskFileService::class)->store($file, $this);
    }


}
