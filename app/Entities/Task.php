<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;


class Task extends Model
{
    use BatoiModels;
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
    protected $inputTypes = [
        'informativa' => ['type' => 'checkbox'],
        'activa' => ['type' => 'checkbox'],
        'vencimiento' => ['type' => 'date'],
    ];


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
        $rolesProfesor = rolesUser($profesor->rol);
        return $query->whereIn('destinatario', $rolesProfesor)
            ->where('activa', 1);
    }

    public function getmyDetailsAttribute()
    {
        $teacher = $teacher?? authUser()->dni;
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
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }



    public function getImageAttribute()
    {
        if ($this->vencimiento <= hoy()) {
            return 'warning.png';
        } else {
            if ($this->informativa) {
                return 'informacion.jpeg';
            } else {
                return 'task.png';
            }
        }
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

    /**
     * @param Request $request
     */
    public function fillFile($file)
    {
        if (!$file->isValid()) {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return ;
        }
        $this->fichero = $file->storeAs(
            'Eventos',
            str_shuffle('abcdefgh123456').'.'.$file->getClientOriginalExtension(),
            'public'
        );
        $this->save();

    }


}
