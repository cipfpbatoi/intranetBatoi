<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    protected $casts = [
        'vencimiento' => 'date'
    ];

    public function Profesor()
    {
        return $this->belongsToMany(Profesor::class, 'tasks_profesores','id_task','id_profesor','id','dni')->withPivot('check','valid')->withTimestamps();
    }

    public function scopeMisTareas($query,$profesor=null)
    {
        $profesor = Profesor::find($profesor) ?? AuthUser();
        $rolesProfesor = RolesUser($profesor->rol);
        return $query->whereIn('destinatario',$rolesProfesor)
            ->where('activa',1)
            ->where('vencimiento','>=',Hoy());
    }



}
