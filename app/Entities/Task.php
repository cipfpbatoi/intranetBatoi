<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    protected $casts = [
        'vencimiento' => 'date'
    ];

    public function Profesores()
    {
        return $this->belongsToMany(Profesor::class, 'tasks_profesores','id_task','id_profesor','id','dni')->withPivot('check','valid')->withTimestamps();
    }

    public function scopeMisTareas($query,$profesor=null)
    {
        $profesor = Profesor::find($profesor) ?? AuthUser();
        $rolesProfesor = RolesUser($profesor->rol);
        return $query->whereIn('destinatario',$rolesProfesor)
            ->where('activa',1);
    }

    public function getmyDetailsAttribute(){
        $teacher = $teacher?? AuthUser()->dni;
        return $this->profesores()->where('dni',$teacher)->first();
    }

    public function getValidAttribute(){
        $taskTeacher = $this->myDetails;
        if (!$taskTeacher) {
            return 0;
        }
        elseif ($taskTeacher->pivot->valid) {
            return  1;
        }
        else {
            return $this->informativa;
        }
    }




}
